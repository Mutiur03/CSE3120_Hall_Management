@extends('layouts.auth')

@section('title', 'Student Login | Hall Management System')

@section('content')
<div class="auth-panel">
    <div class="text-center mb-6 pb-5 border-b border-line">
        <div class="auth-panel__mark" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" />
            </svg>
        </div>
        <h1 class="auth-panel__title">Hall Management System</h1>
        <p class="text-muted text-sm mt-1">Student Login</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-success-soft border border-line text-success px-4 py-3 text-sm" role="status" aria-live="polite">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('student.login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-ink mb-1">Email Address</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                placeholder="name@example.com…"
                required
                autofocus
                autocomplete="username"
                spellcheck="false"
                class="auth-field @error('email') border-danger @enderror"
            >
            @error('email')
                <p class="text-danger text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink mb-1">Password</label>
            <input
                type="password"
                name="password"
                id="password"
                placeholder="Enter password…"
                required
                autocomplete="current-password"
                class="auth-field"
            >
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="rounded border-line text-accent focus:ring-accent">
                <label for="remember" class="ml-2 text-sm text-muted">Remember Me</label>
            </div>
            <a href="{{ route('student.password.request') }}" class="auth-link">Forgot password?</a>
        </div>

        <button type="submit" class="auth-btn">Sign In</button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="auth-link">Admin Login</a>
    </div>
</div>
@endsection
