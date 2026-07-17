@extends('layouts.admin')

@section('title', 'Daily Meal Count')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dining.index') }}">Dining</a></li>
    <li class="breadcrumb-item active">Daily Count</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-chart-bar me-2"></i>Daily Meal Count</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter"></i> Show</button>
            </div>
        </form>

        <h5 class="mb-3">Meal Count for {{ $date->format('M d, Y') }}</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-warning">
                    <div class="card-body text-center">
                        <h3>{{ $breakfastCount }}</h3>
                        <p class="mb-0">Breakfast Registered</p>
                        <small>Present: {{ $breakfastPresent ?? 0 }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $lunchCount }}</h3>
                        <p class="mb-0">Lunch Registered</p>
                        <small>Present: {{ $lunchPresent ?? 0 }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $dinnerCount }}</h3>
                        <p class="mb-0">Dinner Registered</p>
                        <small>Present: {{ $dinnerPresent ?? 0 }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
