@extends('layouts.admin')

@section('title', 'New Room | Hall Management System')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <p class="text-sm text-slate-500 mb-1">
            <a href="{{ route('admin.rooms.index') }}" class="hover:text-slate-700">Rooms</a>
            <span class="mx-1">/</span>
            <span>New Room</span>
        </p>
        <h1 class="text-2xl font-bold text-slate-800">New Room</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.rooms.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('admin.rooms._form', ['room' => null, 'statuses' => $statuses])

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
                    Create Room
                </button>
                <a href="{{ route('admin.rooms.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>

    <p class="text-xs text-slate-500 mt-3">Seats are generated automatically to match the capacity.</p>
</div>
@endsection
