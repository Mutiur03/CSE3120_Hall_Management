@extends('layouts.admin')

@section('title', 'Seat Applications | Hall Management System')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Seat Applications</h1>
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
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Department</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Preference</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Reason</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($applications as $application)
                    <tr>
                        <td class="px-4 py-3 text-slate-600">#{{ $application->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $application->student->user->name }}</div>
                            <div class="text-slate-500">{{ $application->student->roll }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $application->student->department }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            @if ($application->preferred_floor)
                                Floor {{ $application->preferred_floor }}<br>
                            @endif
                            {{ $application->preferredRoom?->room_no ?? 'Any room' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ Str::limit($application->reason, 50) ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusClass = match ($application->status->value) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    default => 'bg-red-100 text-red-800',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($application->status->value) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $application->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($application->isPending())
                                <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="inline" onsubmit="return confirm('Approve this application?');">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 text-xs font-medium">
                                        Approve
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-500">No applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($applications->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
