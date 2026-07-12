@extends('layouts.admin')

@section('title', 'Overview Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-chart-pie me-2"></i>System Overview</h1>
</div>

<div class="stats-grid">
    <div class="stat-card bg-primary"><div class="stat-icon"><i class="fas fa-users"></i></div><div class="stat-info"><h3>{{ $data['total_students'] }}</h3><p>Total Students</p></div></div>
    <div class="stat-card bg-success"><div class="stat-icon"><i class="fas fa-user-check"></i></div><div class="stat-info"><h3>{{ $data['active_students'] }}</h3><p>Active Students</p></div></div>
    <div class="stat-card bg-info"><div class="stat-icon"><i class="fas fa-door-open"></i></div><div class="stat-info"><h3>{{ $data['total_rooms'] }}</h3><p>Total Rooms</p></div></div>
    <div class="stat-card bg-warning"><div class="stat-icon"><i class="fas fa-bed"></i></div><div class="stat-info"><h3>{{ $data['total_seats'] }}</h3><p>Total Seats</p></div></div>
    <div class="stat-card bg-danger"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-info"><h3>{{ $data['occupied_seats'] }}</h3><p>Occupied Seats</p></div></div>
    <div class="stat-card bg-secondary"><div class="stat-icon"><i class="fas fa-circle"></i></div><div class="stat-info"><h3>{{ $data['available_seats'] }}</h3><p>Available Seats</p></div></div>
    <div class="stat-card bg-purple"><div class="stat-icon"><i class="fas fa-file-alt"></i></div><div class="stat-info"><h3>{{ $data['pending_applications'] }}</h3><p>Pending Applications</p></div></div>
    <div class="stat-card bg-orange"><div class="stat-icon"><i class="fas fa-exchange-alt"></i></div><div class="stat-info"><h3>{{ $data['pending_room_changes'] }}</h3><p>Pending Room Changes</p></div></div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>Department Distribution</h5></div>
            <div class="card-body">
                <canvas id="deptChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>Building Occupancy</h5></div>
            <div class="card-body">
                <canvas id="buildChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const deptCtx = document.getElementById('deptChart').getContext('2d');
new Chart(deptCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($data['department_distribution']->pluck('department')) !!},
        datasets: [{
            data: {!! json_encode($data['department_distribution']->pluck('count')) !!},
            backgroundColor: ['#2563eb','#16a34a','#dc2626','#ca8a04','#9333ea','#0891b2','#ea580c','#db2777']
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

const buildCtx = document.getElementById('buildChart').getContext('2d');
new Chart(buildCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($data['building_occupancy']->pluck('building')) !!},
        datasets: [
            { label: 'Total', data: {!! json_encode($data['building_occupancy']->pluck('total')) !!}, backgroundColor: '#2563eb' },
            { label: 'Occupied', data: {!! json_encode($data['building_occupancy']->pluck('occupied')) !!}, backgroundColor: '#dc2626' }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
