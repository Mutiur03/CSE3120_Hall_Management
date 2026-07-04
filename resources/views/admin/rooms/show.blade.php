@extends('layouts.admin')

@section('title', 'Room Details | Hall Management System')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-slate-500 mb-1">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Dashboard</a>
            <span class="mx-1">/</span>
            <a href="{{ route('admin.rooms.index') }}" class="hover:text-slate-700">Rooms</a>
            <span class="mx-1">/</span>
            <span>Room {{ $room->room_no }}</span>
        </p>
        <h1 class="text-2xl font-bold text-slate-800">Room {{ $room->room_no }}</h1>
    </div>
    <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Back to Rooms
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Room Information</h2>
        </div>
        <div class="p-5">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Room Number</dt>
                    <dd class="font-medium text-slate-800">{{ $room->room_no }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Floor</dt>
                    <dd class="font-medium text-slate-800">{{ $room->floor }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Capacity</dt>
                    <dd class="font-medium text-slate-800">{{ $room->capacity }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Status</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $room->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($room->status->value) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Occupancy</dt>
                    <dd class="font-medium text-slate-800">
                        {{ $room->occupiedSeatsCount() }}/{{ $room->capacity }} ({{ $room->occupancyPercentage() }}%)
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Available Seats</dt>
                    <dd class="font-medium text-slate-800">{{ $room->availableSeatsCount() }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Seats</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Seat</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Seat Status</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Occupancy</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-600">Student</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($room->seats as $seat)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $seat->seat_no }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $seat->status->value === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($seat->status->value) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($seat->currentAllocation)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">Occupied</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">Vacant</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $seat->currentAllocation?->student?->user?->name ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No seats configured for this room.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
