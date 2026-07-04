@extends('layouts.auth')

@section('title', 'Forgot Password | Hall Management System')

@section('content')
<div class="bg-white rounded-2xl shadow-2xl p-8">
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-slate-800">Forgot Password</h3>
        <p class="text-slate-500 text-sm mt-1">Enter your email to receive a reset link</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('student.password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                placeholder="Enter your registered email"
                required
                autofocus
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-red-500 @enderror"
            >
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
            Send Reset Link
        </button>
    </form>

    <div class="mt-4 text-center text-sm">
        <a href="{{ route('student.login') }}" class="text-emerald-600 hover:text-emerald-700">Back to Student Login</a>
    </div>
</div>
@endsection
