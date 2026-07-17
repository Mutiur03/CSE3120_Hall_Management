@extends('layouts.admin')

@section('title', 'Seat Statistics')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.seats.index') }}">Seats</a></li>
    <li class="breadcrumb-item active">Statistics</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-chart-bar me-2"></i>Seat Statistics</h1>
</div>

<div class="stats-grid">
    <div class="stat-card bg-primary"><div class="stat-icon"><i class="fas fa-bed"></i></div><div class="stat-info"><h3>{{ $totalSeats }}</h3><p>Total Seats</p></div></div>
    <div class="stat-card bg-danger"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-info"><h3>{{ $occupiedSeats }}</h3><p>Occupied</p></div></div>
    <div class="stat-card bg-success"><div class="stat-icon"><i class="fas fa-circle"></i></div><div class="stat-info"><h3>{{ $availableSeats }}</h3><p>Available</p></div></div>
    <div class="stat-card bg-warning"><div class="stat-icon"><i class="fas fa-tools"></i></div><div class="stat-info"><h3>{{ $maintenanceSeats }}</h3><p>Maintenance</p></div></div>
</div>

<div class="card mt-4">
    <div class="card-header"><h5>Overall Occupancy</h5></div>
    <div class="card-body">
        <div class="progress" style="height: 30px;">
            <div class="progress-bar bg-danger" style="width: {{ $occupancyPercentage }}%">{{ $occupancyPercentage }}% Occupied</div>
            <div class="progress-bar bg-success" style="width: {{ 100 - $occupancyPercentage }}%">{{ 100 - $occupancyPercentage }}% Available</div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>Building-wise Statistics</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Building</th><th>Total</th><th>Occupied</th><th>Available</th><th>Rate</th></tr></thead>
                        <tbody>
                            @foreach($buildingStats as $stat)
                            <tr>
                                <td>{{ $stat->building }}</td>
                                <td>{{ $stat->total_seats }}</td>
                                <td>{{ $stat->occupied }}</td>
                                <td>{{ $stat->available }}</td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $stat->occupancy_rate > 80 ? 'danger' : 'success' }}" style="width: {{ $stat->occupancy_rate }}%"></div>
                                    </div>
                                    <small>{{ $stat->occupancy_rate }}%</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>Floor-wise Occupancy</h5></div>
            <div class="card-body">
                <canvas id="floorChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('floorChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($floorStats->pluck('floor')->map(fn($f) => 'Floor '.$f)) !!},
        datasets: [
            { label: 'Total Seats', data: {!! json_encode($floorStats->pluck('total_seats')) !!}, backgroundColor: '#2563eb' },
            { label: 'Occupied', data: {!! json_encode($floorStats->pluck('occupied')) !!}, backgroundColor: '#dc2626' }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
