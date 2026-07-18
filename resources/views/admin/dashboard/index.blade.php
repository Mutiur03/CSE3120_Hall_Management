@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="dashboard-header">
    <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h1>
    <p class="text-muted">Welcome to Hall Management System</p>
</div>

<div class="stats-grid">
    <div class="stat-card bg-primary">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>{{ $totalStudents }}</h3>
            <p>Total Students</p>
        </div>
    </div>
    <div class="stat-card bg-success">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <h3>{{ $activeStudents }}</h3>
            <p>Active Students</p>
        </div>
    </div>
    <div class="stat-card bg-info">
        <div class="stat-icon"><i class="fas fa-door-open"></i></div>
        <div class="stat-info">
            <h3>{{ $totalRooms }}</h3>
            <p>Total Rooms</p>
        </div>
    </div>
    <div class="stat-card bg-warning">
        <div class="stat-icon"><i class="fas fa-bed"></i></div>
        <div class="stat-info">
            <h3>{{ $totalSeats }}</h3>
            <p>Total Seats</p>
        </div>
    </div>
    <div class="stat-card bg-danger">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3>{{ $occupiedSeats }}</h3>
            <p>Occupied Seats</p>
        </div>
    </div>
    <div class="stat-card bg-secondary">
        <div class="stat-icon"><i class="fas fa-circle"></i></div>
        <div class="stat-info">
            <h3>{{ $availableSeats }}</h3>
            <p>Available Seats</p>
        </div>
    </div>
    <div class="stat-card bg-purple">
        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info">
            <h3>{{ $pendingApplications }}</h3>
            <p>Pending Applications</p>
        </div>
    </div>
    <div class="stat-card bg-orange">
        <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
        <div class="stat-info">
            <h3>{{ $pendingRoomChanges }}</h3>
            <p>Pending Room Changes</p>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-chart-line me-2"></i>Seat Allocation Trends</h5>
            </div>
            <div class="card-body">
                <canvas id="allocationChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i>Department Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="departmentChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock me-2"></i>Recent Allocations</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Room</th>
                                <th>Seat</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAllocations as $allocation)
                            <tr>
                                <td>{{ $allocation->student->user->name }}</td>
                                <td>{{ $allocation->seat?->room?->room_no ?? '—' }}</td>
                                <td>{{ $allocation->seat?->seat_no ?? '—' }}</td>
                                <td>{{ $allocation->allocated_at?->format('M d, Y') ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3">No recent allocations</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bell me-2"></i>Recent Applications</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Preferred Floor</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications as $app)
                            <tr>
                                <td>{{ $app->student->user->name }}</td>
                                <td>{{ $app->preferred_floor ?? 'Any' }}</td>
                                <td><span class="badge bg-{{ $app->status->value === 'pending' ? 'warning' : ($app->status->value === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($app->status->value) }}</span></td>
                                <td>{{ $app->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3">No recent applications</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-building me-2"></i>Building Occupancy</h5>
            </div>
            <div class="card-body">
                <canvas id="buildingChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-utensils me-2"></i>Today's Meal Count</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-coffee fa-2x text-warning mb-2"></i>
                            <h4>{{ $breakfastCount }}</h4>
                            <p class="text-muted">Breakfast</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-hamburger fa-2x text-success mb-2"></i>
                            <h4>{{ $lunchCount }}</h4>
                            <p class="text-muted">Lunch</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-utensils fa-2x text-info mb-2"></i>
                            <h4>{{ $dinnerCount }}</h4>
                            <p class="text-muted">Dinner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const allocationCtx = document.getElementById('allocationChart').getContext('2d');
new Chart(allocationCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [{
            label: 'Seat Allocations',
            data: {!! json_encode($allocationData) !!},
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

const deptCtx = document.getElementById('departmentChart').getContext('2d');
new Chart(deptCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($departments) !!},
        datasets: [{
            data: {!! json_encode($deptCounts) !!},
            backgroundColor: ['#2563eb', '#16a34a', '#dc2626', '#ca8a04', '#9333ea', '#0891b2', '#ea580c', '#db2777']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

const buildingCtx = document.getElementById('buildingChart').getContext('2d');
new Chart(buildingCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($buildingStats->pluck('building')) !!},
        datasets: [
            { label: 'Occupied', data: {!! json_encode($buildingStats->pluck('occupied')) !!}, backgroundColor: '#dc2626' },
            { label: 'Available', data: {!! json_encode($buildingStats->pluck('available')) !!}, backgroundColor: '#16a34a' }
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
