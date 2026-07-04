@extends('layouts.admin')

@section('title', 'Change Password | Hall Management System')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Change Password</h1>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.change-password') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    name="current_password"
                    id="current_password"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror"
                >
                @error('current_password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-slate-700 mb-1">
                    New Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    name="new_password"
                    id="new_password"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('new_password') border-red-500 @enderror"
                >
                @error('new_password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    name="new_password_confirmation"
                    id="new_password_confirmation"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
                Change Password
            </button>
        </form>
    </div>
</div>
@endsection
