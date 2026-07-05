@extends('layouts.student')

@section('title', 'Update Contact | Hall Management System')

@section('content')
<div class="mb-6">
    <p class="text-sm text-slate-500 mb-1">
        <a href="{{ route('student.dashboard') }}" class="hover:text-slate-700">Dashboard</a>
        <span class="mx-1">/</span>
        <a href="{{ route('student.profile') }}" class="hover:text-slate-700">My Profile</a>
        <span class="mx-1">/</span>
        <span>Update Contact</span>
    </p>
    <h1 class="text-2xl font-bold text-slate-800">Update Contact Information</h1>
    <p class="text-slate-600 mt-1">You can update your phone number. Other details are managed by the hall admin.</p>
</div>

<div class="max-w-lg bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">
                Phone <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="phone"
                id="phone"
                value="{{ old('phone', $student->phone) }}"
                required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
            >
            @error('phone')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
                Save Changes
            </button>
            <a href="{{ route('student.profile') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
        </div>
    </form>
</div>
@endsection
