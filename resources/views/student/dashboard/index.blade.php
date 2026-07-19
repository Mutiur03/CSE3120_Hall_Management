@extends('layouts.student')

@section('title', 'Student Dashboard | Hall Management System')

@section('content')
<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p>Welcome, {{ auth()->user()->name }}</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Quick Links</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('student.profile') }}" class="list-group-item list-group-item-action">View Profile</a>
        <a href="{{ route('student.seat') }}" class="list-group-item list-group-item-action">My Seat Allocation</a>
        <a href="{{ route('student.applications.index') }}" class="list-group-item list-group-item-action">My Applications</a>
        <a href="{{ route('student.room-changes.index') }}" class="list-group-item list-group-item-action">Room Change Requests</a>
    </div>
</div>
@endsection
