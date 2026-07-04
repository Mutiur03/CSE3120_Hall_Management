@extends('layouts.student')

@section('title', 'Student Dashboard | Hall Management System')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Student Dashboard</h1>
    <p class="text-slate-600">Welcome, {{ auth()->user()->name }}.</p>
</div>
@endsection
