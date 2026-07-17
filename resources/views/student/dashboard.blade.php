@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-header mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i>Welcome, {{ $student->name }}!</h2>
    <p class="text-muted">Student ID: {{ $student->student_id }} | Department: {{ $student->department }}</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-bed me-2"></i>Current Accommodation</h5>
            </div>
            <div class="card-body">
                @if($currentRoom && $currentSeat)
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Room Number:</strong> {{ $currentRoom->room_number }}</p>
                            <p><strong>Building:</strong> {{ $currentRoom->building }}</p>
                            <p><strong>Floor:</strong> {{ $currentRoom->floor }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Seat:</strong> {{ $currentSeat->seat_number }}</p>
                            <p><strong>Room Type:</strong> {{ ucfirst($currentRoom->room_type) }}</p>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>You do not have an allocated seat yet.
                        <a href="{{ route('student.applications.create') }}" class="alert-link">Apply for a seat</a>.
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-file-alt me-2"></i>Application Status</h5>
                    </div>
                    <div class="card-body">
                        @if($pendingApplication)
                            <div class="alert alert-warning mb-0">
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-warning">{{ ucfirst($pendingApplication->status) }}</span></p>
                                <p class="mb-1"><strong>Submitted:</strong> {{ $pendingApplication->created_at->format('M d, Y') }}</p>
                                <p class="mb-0"><strong>Preferred Building:</strong> {{ $pendingApplication->preferred_building ?? 'Any' }}</p>
                            </div>
                        @else
                            <p class="text-muted">No pending application.</p>
                            @if(!$currentRoom)
                                <a href="{{ route('student.applications.create') }}" class="btn btn-primary btn-sm">Apply Now</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-exchange-alt me-2"></i>Room Change Status</h5>
                    </div>
                    <div class="card-body">
                        @if($pendingRoomChange)
                            <div class="alert alert-info mb-0">
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-info">{{ ucfirst($pendingRoomChange->status) }}</span></p>
                                <p class="mb-1"><strong>Current:</strong> {{ $pendingRoomChange->currentRoom->room_number }}</p>
                                <p class="mb-0"><strong>Requested:</strong> {{ $pendingRoomChange->requestedRoom->room_number }}</p>
                            </div>
                        @else
                            <p class="text-muted">No pending room change request.</p>
                            @if($currentRoom)
                                <a href="{{ route('student.room-changes.create') }}" class="btn btn-primary btn-sm">Request Change</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-utensils me-2"></i>Today's Meal Status</h5>
            </div>
            <div class="card-body">
                @if($todayMeal)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Meal Status</span>
                        <span class="badge bg-{{ $todayMeal->meal_active ? 'success' : 'danger' }}">
                            {{ $todayMeal->meal_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Meals:</small>
                        <span class="badge bg-{{ $todayMeal->breakfast ? 'success' : 'secondary' }} me-1">Breakfast</span>
                        <span class="badge bg-{{ $todayMeal->lunch ? 'success' : 'secondary' }} me-1">Lunch</span>
                        <span class="badge bg-{{ $todayMeal->dinner ? 'success' : 'secondary' }}">Dinner</span>
                    </div>
                    <form action="{{ route('student.dining.toggle') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-{{ $todayMeal->meal_active ? 'warning' : 'success' }}">
                            <i class="fas fa-{{ $todayMeal->meal_active ? 'pause' : 'play' }} me-1"></i>
                            Turn Meal {{ $todayMeal->meal_active ? 'Off' : 'On' }}
                        </button>
                    </form>
                @else
                    <p class="text-muted">No meal record for today.</p>
                    <form action="{{ route('student.dining.toggle') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="fas fa-play me-1"></i>Activate Meal</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i>Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('student.profile') }}" class="list-group-item list-group-item-action"><i class="fas fa-user me-2"></i>View Profile</a>
                    <a href="{{ route('student.applications.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-file-alt me-2"></i>My Applications</a>
                    <a href="{{ route('student.room-changes.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-exchange-alt me-2"></i>Room Changes</a>
                    <a href="{{ route('student.dining.status') }}" class="list-group-item list-group-item-action"><i class="fas fa-utensils me-2"></i>Dining Status</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
