@extends('layouts.auth')

@section('title', 'Admin Login | Hall Management System')

@section('content')
<div class="bg-white rounded-2xl shadow-2xl p-8">
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-100 text-blue-600 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6v11.25A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75V9z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800">Hall Management System</h3>
        <p class="text-slate-500 text-sm mt-1">Admin Login</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                placeholder="Enter email"
                required
                autofocus
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
            >
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input
                type="password"
                name="password"
                id="password"
                placeholder="Enter password"
                required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="remember" class="ml-2 text-sm text-slate-600">Remember Me</label>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
            Login
        </button>
    </form>

    <div class="mt-4 text-center text-sm">
        <a href="{{ route('student.login') }}" class="text-blue-600 hover:text-blue-700">Student Login</a>
    </div>
</div>
@endsection
