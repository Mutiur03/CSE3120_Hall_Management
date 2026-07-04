@extends('layouts.auth')

@section('title', 'Reset Password | Hall Management System')

@section('content')
<div class="bg-white rounded-2xl shadow-2xl p-8">
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-slate-800">Reset Password</h3>
        <p class="text-slate-500 text-sm mt-1">Choose a new password for your account</p>
    </div>

    <form method="POST" action="{{ route('student.password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $email) }}"
                required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-red-500 @enderror"
            >
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
            <input
                type="password"
                name="password"
                id="password"
                required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('password') border-red-500 @enderror"
            >
            @error('password')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
            >
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
            Reset Password
        </button>
    </form>

    <div class="mt-4 text-center text-sm">
        <a href="{{ route('student.login') }}" class="text-emerald-600 hover:text-emerald-700">Back to Student Login</a>
    </div>
</div>
@endsection
