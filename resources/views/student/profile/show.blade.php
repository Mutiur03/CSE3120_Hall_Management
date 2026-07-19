@extends('layouts.student')

@section('title', 'My Profile | Hall Management System')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-sm text-slate-500 mb-1">
            <a href="{{ route('student.dashboard') }}" class="hover:text-slate-700">Dashboard</a>
            <span class="mx-1">/</span>
            <span>My Profile</span>
        </p>
        <h1 class="text-2xl font-bold text-slate-800">My Profile</h1>
    </div>
    <a href="{{ route('student.profile.edit') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Update Contact
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="p-6 text-center">
            <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center rounded-full text-4xl font-semibold" style="background: var(--accent-soft, #e4ebe8); color: var(--accent, #3d524c);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 class="text-xl font-semibold text-slate-800">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500 mt-1">Roll: {{ $student->roll }}</p>
            <span class="inline-flex mt-3 rounded-full px-3 py-1 text-xs font-medium {{ $student->status->value === 'active' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                {{ ucfirst($student->status->value) }}
            </span>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Personal Information</h2>
        </div>
        <div class="p-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-slate-500">Registration Number</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $student->registration_no }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Department</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $student->department }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Academic Session</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $student->academic_session }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Phone</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $student->phone }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-medium text-slate-800 mt-1">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Current Seat</dt>
                    <dd class="font-medium text-slate-800 mt-1">
                        @if($student->currentAllocation)
                            Room {{ $student->currentAllocation->seat->room->room_no }}, Seat {{ $student->currentAllocation->seat->seat_no }}
                        @else
                            Not allocated
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
