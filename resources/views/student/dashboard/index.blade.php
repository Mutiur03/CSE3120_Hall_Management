@extends('layouts.student')

@section('title', 'Student Dashboard | Hall Management System')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Student Dashboard</h1>
    <p class="text-slate-600 mb-6">Welcome, {{ auth()->user()->name }}.</p>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Quick Links</h2>
        </div>
        <div class="p-2">
            <a href="{{ route('student.profile') }}" class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                View Profile
            </a>
            <a href="{{ route('student.seat') }}" class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                My Seat Allocation
            </a>
            <a href="{{ route('student.applications.index') }}" class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                My Applications
            </a>
            <a href="{{ route('student.room-changes.index') }}" class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Room Change Requests
            </a>
        </div>
    </div>
</div>
@endsection
