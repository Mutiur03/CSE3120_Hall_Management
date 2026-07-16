@extends('layouts.student')

@section('title', 'Request Room Change | Hall Management System')

@section('content')
<div class="mb-6">
    <p class="text-sm text-slate-500 mb-1">
        <a href="{{ route('student.room-changes.index') }}" class="hover:text-slate-700">My Room Changes</a>
        <span class="mx-1">/</span>
        <span>Request Room Change</span>
    </p>
    <h1 class="text-2xl font-bold text-slate-800">Room Change Request</h1>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Your current allocation</h2>
        </div>
        <div class="px-5 py-4 border-b border-slate-200 text-sm text-slate-600">
            <p>
                Room <span class="font-medium text-slate-800">{{ $allocation->seat?->room?->room_no ?? '—' }}</span>
                (Floor {{ $allocation->seat?->room?->floor ?? '—' }}),
                Seat <span class="font-medium text-slate-800">{{ $allocation->seat?->seat_no ?? '—' }}</span>
            </p>
        </div>
        <form action="{{ route('student.room-changes.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label for="requested_room_id" class="block text-sm font-medium text-slate-700 mb-1">Requested Room</label>
                <select id="requested_room_id" name="requested_room_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select a room</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('requested_room_id') == $room->id)>
                            Room {{ $room->room_no }} (Floor {{ $room->floor }})
                        </option>
                    @endforeach
                </select>
                @error('requested_room_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="reason" class="block text-sm font-medium text-slate-700 mb-1">Reason (optional)</label>
                <textarea id="reason" name="reason" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">
                Submit Request
            </button>
        </form>
    </div>
</div>
@endsection
