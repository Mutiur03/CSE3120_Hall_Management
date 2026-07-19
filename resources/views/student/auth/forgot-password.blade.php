@extends('layouts.auth')

@section('title', 'Forgot Password | Hall Management System')

@section('content')
<div class="auth-panel">
    <div class="text-center mb-6 pb-5 border-b border-line">
        <h1 class="auth-panel__title">Forgot Password</h1>
        <p class="text-muted text-sm mt-1">Enter your email to receive a reset link</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-success-soft border border-line text-success px-4 py-3 text-sm" role="status" aria-live="polite">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('student.password.email') }}" class="space-y-4">
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

        <button type="submit" class="auth-btn">Send Reset Link</button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('student.login') }}" class="auth-link">Back to Student Login</a>
    </div>
</div>
@endsection
