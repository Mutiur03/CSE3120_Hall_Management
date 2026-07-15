@extends('layouts.student')

@section('title', 'My Seat Allocation | Hall Management System')

@section('content')
<div class="mb-6">
    <p class="text-sm text-slate-500 mb-1">
        <a href="{{ route('student.dashboard') }}" class="hover:text-slate-700">Dashboard</a>
        <span class="mx-1">/</span>
        <span>My Seat Allocation</span>
    </p>
    <h1 class="text-2xl font-bold text-slate-800">My Seat Allocation</h1>
</div>

@if ($allocation)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 max-w-2xl">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Current Allocation</h2>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium bg-green-100 text-green-700">
                {{ ucfirst($allocation->status->value) }}
            </span>
        </div>
        <div class="p-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-slate-500">Room Number</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $allocation->seat->room->room_no }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Floor</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $allocation->seat->room->floor }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Seat Number</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $allocation->seat->seat_no }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Allocated On</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $allocation->allocated_at?->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>
@else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 max-w-2xl">
        <div class="p-8 text-center">
            <h2 class="font-semibold text-slate-800 mb-1">No seat allocated</h2>
            <p class="text-sm text-slate-500 mb-5">You do not have an active seat allocation yet.</p>
            <a href="{{ route('student.applications.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Apply for a Seat
            </a>
        </div>
    </div>
@endif
@endsection
