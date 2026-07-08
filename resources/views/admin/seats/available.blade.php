@extends('layouts.admin')

@section('title', 'Available Seats | Hall Management System')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Available Seats</h1>
    <a href="{{ route('admin.seats.allocate-form') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">
        Allocate Seat
    </a>
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
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Allocate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($seats as $seat)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $seat->seat_no }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->room->room_no }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $seat->room->floor }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.seats.allocate') }}" method="POST" class="flex items-center justify-end gap-2">
                                @csrf
                                <input type="hidden" name="seat_id" value="{{ $seat->id }}">
                                <select name="student_id" class="rounded-lg border border-slate-300 px-2 py-1 text-sm" required>
                                    <option value="">Select student</option>
                                    @foreach ($unallocatedStudents as $student)
                                        <option value="{{ $student->id }}">{{ $student->roll }} - {{ $student->user->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded-lg bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 text-sm font-medium">Allocate</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">No available seats found.</td>
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
