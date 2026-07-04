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
   
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i>Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('student.profile') }}" class="list-group-item list-group-item-action"><i class="fas fa-user me-2"></i>View Profile</a>
                   
        </div>
    </div>
</div>
@endsection
