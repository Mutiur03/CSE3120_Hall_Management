@extends('layouts.admin')

@section('title', 'Seats | Hall Management System')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Seats</h1>
    <a href="{{ route('admin.seats.allocate-form') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">
        Allocate Seat
    </a>
</div>

<form method="GET" class="mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
    <select name="floor" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Floors</option>
        @foreach ($floors as $floor)
            <option value="{{ $floor }}" @selected(request('floor') == $floor)>Floor {{ $floor }}</option>
        @endforeach
    </select>
    <select name="room_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Rooms</option>
        @foreach ($rooms as $room)
            <option value="{{ $room->id }}" @selected(request('room_id') == $room->id)>{{ $room->room_no }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Status</option>
        <option value="available" @selected(request('status') === 'available')>Available</option>
        <option value="occupied" @selected(request('status') === 'occupied')>Occupied</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
    </select>
    <button type="submit" class="rounded-lg bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-medium">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Seat</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Room</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Floor</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Student</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($seats as $seat)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $seat->seat_no }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->room->room_no }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->room->floor }}</td>
                        <td class="px-4 py-3">
                            @if ($seat->currentAllocation)
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">Occupied</span>
                            @elseif ($seat->status->value === 'active')
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">Available</span>
                            @else
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-slate-100 text-slate-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->currentAllocation?->student->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($seat->currentAllocation)
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.seats.vacate-form', $seat) }}" class="text-amber-600 hover:text-amber-800 font-medium">Vacate</a>
                                    <a href="{{ route('admin.seats.transfer-form', $seat) }}" class="text-blue-600 hover:text-blue-800 font-medium">Transfer</a>
                                </div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No seats found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($seats->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">{{ $seats->links() }}</div>
    @endif
</div>
@endsection
