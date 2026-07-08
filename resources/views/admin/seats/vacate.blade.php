@extends('layouts.admin')

@section('title', 'Vacate Seat | Hall Management System')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Vacate Seat</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-xl">
    <dl class="space-y-3 text-sm mb-6">
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Seat</dt>
            <dd class="font-medium text-slate-800">{{ $seat->seat_no }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Room</dt>
            <dd class="font-medium text-slate-800">{{ $seat->room->room_no }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Student</dt>
            <dd class="font-medium text-slate-800">{{ $seat->currentAllocation->student->user->name }}</dd>
        </div>
        <div class="flex justify-between gap-4">
            <dt class="text-slate-500">Roll</dt>
            <dd class="font-medium text-slate-800">{{ $seat->currentAllocation->student->roll }}</dd>
        </div>
    </dl>

    <form action="{{ route('admin.seats.vacate', $seat) }}" method="POST" class="flex items-center gap-3">
        @csrf
        <button type="submit" class="rounded-lg bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 text-sm font-medium" onclick="return confirm('Vacate this seat?')">
            Confirm Vacate
        </button>
        <a href="{{ route('admin.seats.occupied') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
    </form>
</div>
@endsection
