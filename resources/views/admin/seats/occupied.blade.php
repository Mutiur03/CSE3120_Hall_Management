@extends('layouts.admin')

@section('title', 'Occupied Seats | Hall Management System')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Occupied Seats</h1>
</div>

<form method="GET" class="mb-4 flex gap-3">
    <select name="floor" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Floors</option>
        @foreach ($floors as $floor)
            <option value="{{ $floor }}" @selected(request('floor') == $floor)>Floor {{ $floor }}</option>
        @endforeach
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
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Student</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Roll</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($seats as $seat)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $seat->seat_no }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->room->room_no }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->room->floor }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->currentAllocation?->student->user->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->currentAllocation?->student->roll }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.seats.vacate-form', $seat) }}" class="text-amber-600 hover:text-amber-800 font-medium">Vacate</a>
                                <a href="{{ route('admin.seats.transfer-form', $seat) }}" class="text-blue-600 hover:text-blue-800 font-medium">Transfer</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No occupied seats found.</td>
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
