@extends('layouts.auth')

@section('title', 'Reset Password | Hall Management System')

@section('content')
<div class="auth-panel">
    <div class="text-center mb-6 pb-5 border-b border-line">
        <h1 class="auth-panel__title">Reset Password</h1>
        <p class="text-muted text-sm mt-1">Choose a new password for your account</p>
    </div>

    <form method="POST" action="{{ route('student.password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium text-ink mb-1">Email Address</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $email) }}"
                required
                autocomplete="username"
                spellcheck="false"
                class="auth-field @error('email') border-danger @enderror"
            >
            @error('email')
                <p class="text-danger text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink mb-1">New Password</label>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="new-password"
                placeholder="Enter new password…"
                class="auth-field @error('password') border-danger @enderror"
            >
            @error('password')
                <p class="text-danger text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-ink mb-1">Confirm New Password</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirm new password…"
                class="auth-field"
            >
        </div>

        <button type="submit" class="auth-btn">Reset Password</button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('student.login') }}" class="auth-link">Back to Student Login</a>
    </div>
</div>
@endsection
