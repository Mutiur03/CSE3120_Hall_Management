@extends('layouts.admin')

@section('title', 'Edit Room | Hall Management System')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <p class="text-sm text-slate-500 mb-1">
            <a href="{{ route('admin.rooms.index') }}" class="hover:text-slate-700">Rooms</a>
            <span class="mx-1">/</span>
            <a href="{{ route('admin.rooms.show', $room) }}" class="hover:text-slate-700">Room {{ $room->room_no }}</a>
            <span class="mx-1">/</span>
            <span>Edit</span>
        </p>
        <h1 class="text-2xl font-bold text-slate-800">Edit Room {{ $room->room_no }}</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.rooms.update', $room) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.rooms._form', ['room' => $room, 'statuses' => $statuses])

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
                    Save Changes
                </button>
                <a href="{{ route('admin.rooms.show', $room) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>

    <p class="text-xs text-slate-500 mt-3">Raising capacity adds seats; lowering it removes only vacant seats.</p>
</div>
@endsection
