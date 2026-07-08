@extends('layouts.admin')

@section('title', 'Allocate Seat | Hall Management System')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Allocate Seat</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-xl">
    <form action="{{ route('admin.seats.allocate') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="seat_id" class="block text-sm font-medium text-slate-700 mb-1">Seat</label>
            <select id="seat_id" name="seat_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                <option value="">Select seat</option>
                @foreach ($seats as $seat)
                    <option value="{{ $seat->id }}" @selected(old('seat_id') == $seat->id)>
                        {{ $seat->seat_no }} (Room {{ $seat->room->room_no }}, Floor {{ $seat->room->floor }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="student_id" class="block text-sm font-medium text-slate-700 mb-1">Student</label>
            <select id="student_id" name="student_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                <option value="">Select student</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                        {{ $student->roll }} - {{ $student->user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">Allocate</button>
            <a href="{{ route('admin.seats.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
