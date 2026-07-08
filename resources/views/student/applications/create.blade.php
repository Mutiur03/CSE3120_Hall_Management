@extends('layouts.student')

@section('title', 'Apply for Seat | Hall Management System')

@section('content')
<div class="mb-6">
    <p class="text-sm text-slate-500 mb-1">
        <a href="{{ route('student.applications.index') }}" class="hover:text-slate-700">My Applications</a>
        <span class="mx-1">/</span>
        <span>Apply for Seat</span>
    </p>
    <h1 class="text-2xl font-bold text-slate-800">Seat Application</h1>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Submit your preferences</h2>
        </div>
        <form action="{{ route('student.applications.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label for="preferred_floor" class="block text-sm font-medium text-slate-700 mb-1">Preferred Floor</label>
                <select id="preferred_floor" name="preferred_floor" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">No preference</option>
                    @foreach ($floors as $floor)
                        <option value="{{ $floor }}" @selected(old('preferred_floor') == $floor)>Floor {{ $floor }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="preferred_room_id" class="block text-sm font-medium text-slate-700 mb-1">Preferred Room</label>
                <select id="preferred_room_id" name="preferred_room_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">No preference</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('preferred_room_id') == $room->id)>
                            Room {{ $room->room_no }} (Floor {{ $room->floor }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="reason" class="block text-sm font-medium text-slate-700 mb-1">Reason (optional)</label>
                <textarea id="reason" name="reason" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
            </div>
            <button type="submit" class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-medium">
                Submit Application
            </button>
        </form>
    </div>
</div>
@endsection
