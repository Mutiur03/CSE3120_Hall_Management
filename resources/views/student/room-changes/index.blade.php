@extends('layouts.student')

@section('title', 'My Room Changes | Hall Management System')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-sm text-slate-500 mb-1">
            <a href="{{ route('student.dashboard') }}" class="hover:text-slate-700">Dashboard</a>
            <span class="mx-1">/</span>
            <span>My Room Changes</span>
        </p>
        <h1 class="text-2xl font-bold text-slate-800">My Room Change Requests</h1>
    </div>
    @if ($student->currentAllocation && ! $requests->contains(fn ($req) => $req->status->value === 'pending'))
        <a href="{{ route('student.room-changes.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">
            New Request
        </a>
    @endif
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">ID</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Current Room</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Requested Room</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Reason</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Admin Comment</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($requests as $request)
                    <tr>
                        <td class="px-4 py-3 text-slate-600">#{{ $request->id }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $request->currentSeat?->room?->room_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $request->requestedRoom?->room_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $request->reason ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusClass = match ($request->status->value) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    default => 'bg-red-100 text-red-800',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($request->status->value) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $request->admin_comment ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $request->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('student.room-changes.show', $request) }}" class="text-blue-600 hover:text-blue-700 font-medium">View</a>
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
