@extends('layouts.admin')

@section('title', 'Room Change Requests | Hall Management System')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Room Change Requests</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by roll or name..."
                class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm"
            >
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-medium">
                Filter
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">ID</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Student</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Current Room</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Requested Room</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Reason</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($requests as $roomChange)
                    @php
                        $availableSeats = $roomChange->requestedRoom
                            ? $roomChange->requestedRoom->seats->filter(
                                fn ($seat) => $seat->status->value === 'active' && $seat->currentAllocation === null
                            )
                            : collect();
                    @endphp
                    <tr>
                        <td class="px-4 py-3 text-slate-600">#{{ $roomChange->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $roomChange->student->user->name }}</div>
                            <div class="text-slate-500">{{ $roomChange->student->roll }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $roomChange->currentSeat?->room?->room_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $roomChange->requestedRoom?->room_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ Str::limit($roomChange->reason, 50) ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusClass = match ($roomChange->status->value) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    default => 'bg-red-100 text-red-800',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($roomChange->status->value) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $roomChange->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($roomChange->isPending())
                                <div class="flex flex-col items-end gap-2">
                                    @if ($availableSeats->isNotEmpty())
                                        <form action="{{ route('admin.room-changes.approve', $roomChange) }}" method="POST" class="inline-flex items-center gap-2" onsubmit="return confirm('Approve and transfer this student?');">
                                            @csrf
                                            <select name="target_seat_id" required class="w-40 rounded-lg border border-slate-300 px-2 py-1.5 text-xs">
                                                @foreach ($availableSeats as $seat)
                                                    <option value="{{ $seat->id }}">Seat {{ $seat->seat_no }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-lg bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 text-xs font-medium">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-amber-600">No available seats in requested room</span>
                                    @endif
                                    <form action="{{ route('admin.room-changes.reject', $roomChange) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input
                                            type="text"
                                            name="admin_comment"
                                            required
                                            placeholder="Rejection reason"
                                            class="w-40 rounded-lg border border-slate-300 px-2 py-1.5 text-xs"
                                        >
                                        <button type="submit" class="rounded-lg bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 text-xs font-medium">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-500">No room change requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($requests->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
