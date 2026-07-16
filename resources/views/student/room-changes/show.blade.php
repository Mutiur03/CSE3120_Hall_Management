@extends('layouts.student')

@section('title', 'Request Status | Hall Management System')

@section('content')
<div class="mb-6">
    <p class="text-sm text-slate-500 mb-1">
        <a href="{{ route('student.room-changes.index') }}" class="hover:text-slate-700">My Room Changes</a>
        <span class="mx-1">/</span>
        <span>Request #{{ $roomChange->id }}</span>
    </p>
    <h1 class="text-2xl font-bold text-slate-800">Room Change Request Status</h1>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Request #{{ $roomChange->id }}</h2>
            @php
                $statusClass = match ($roomChange->status->value) {
                    'pending' => 'bg-amber-100 text-amber-800',
                    'approved' => 'bg-green-100 text-green-800',
                    default => 'bg-red-100 text-red-800',
                };
            @endphp
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">
                {{ ucfirst($roomChange->status->value) }}
            </span>
        </div>

        <dl class="divide-y divide-slate-200 text-sm">
            <div class="px-5 py-3 flex justify-between gap-4">
                <dt class="text-slate-500">Current Room</dt>
                <dd class="text-slate-800 font-medium text-right">
                    {{ $roomChange->currentSeat?->room?->room_no ?? '—' }}
                    @if ($roomChange->currentSeat?->room)
                        <span class="text-slate-500 font-normal">(Floor {{ $roomChange->currentSeat->room->floor }})</span>
                    @endif
                </dd>
            </div>
            <div class="px-5 py-3 flex justify-between gap-4">
                <dt class="text-slate-500">Current Seat</dt>
                <dd class="text-slate-800 font-medium text-right">{{ $roomChange->currentSeat?->seat_no ?? '—' }}</dd>
            </div>
            <div class="px-5 py-3 flex justify-between gap-4">
                <dt class="text-slate-500">Requested Room</dt>
                <dd class="text-slate-800 font-medium text-right">
                    {{ $roomChange->requestedRoom?->room_no ?? '—' }}
                    @if ($roomChange->requestedRoom)
                        <span class="text-slate-500 font-normal">(Floor {{ $roomChange->requestedRoom->floor }})</span>
                    @endif
                </dd>
            </div>
            <div class="px-5 py-3 flex justify-between gap-4">
                <dt class="text-slate-500">Reason</dt>
                <dd class="text-slate-800 text-right">{{ $roomChange->reason ?? '—' }}</dd>
            </div>
            <div class="px-5 py-3 flex justify-between gap-4">
                <dt class="text-slate-500">Submitted</dt>
                <dd class="text-slate-800 text-right">{{ $roomChange->created_at->format('M d, Y g:i A') }}</dd>
            </div>
            <div class="px-5 py-3 flex justify-between gap-4">
                <dt class="text-slate-500">Reviewed</dt>
                <dd class="text-slate-800 text-right">{{ $roomChange->reviewed_at?->format('M d, Y g:i A') ?? 'Not yet reviewed' }}</dd>
            </div>
            @if ($roomChange->admin_comment)
                <div class="px-5 py-3">
                    <dt class="text-slate-500 mb-1">Admin Comment</dt>
                    <dd class="text-slate-800">{{ $roomChange->admin_comment }}</dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="mt-4">
        <a href="{{ route('student.room-changes.index') }}" class="text-sm text-blue-600 hover:text-blue-700">&larr; Back to my requests</a>
    </div>
</div>
@endsection
