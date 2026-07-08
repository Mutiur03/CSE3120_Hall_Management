@extends('layouts.admin')

@section('title', 'Transfer Seat | Hall Management System')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Transfer Seat</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-xl">
    <dl class="space-y-3 text-sm mb-6">
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Current Seat</dt>
            <dd class="font-medium text-slate-800">{{ $seat->seat_no }} (Room {{ $seat->room->room_no }})</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Student</dt>
            <dd class="font-medium text-slate-800">{{ $seat->currentAllocation->student->user->name }}</dd>
        </div>
    </dl>

    <form action="{{ route('admin.seats.transfer', $seat) }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="target_seat_id" class="block text-sm font-medium text-slate-700 mb-1">Target Seat</label>
            <select id="target_seat_id" name="target_seat_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                <option value="">Select target seat</option>
                @foreach ($targetSeats as $targetSeat)
                    <option value="{{ $targetSeat->id }}" @selected(old('target_seat_id') == $targetSeat->id)>
                        {{ $targetSeat->seat_no }} (Room {{ $targetSeat->room->room_no }}, Floor {{ $targetSeat->room->floor }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">Transfer</button>
            <a href="{{ route('admin.seats.occupied') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
