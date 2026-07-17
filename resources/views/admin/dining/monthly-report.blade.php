@extends('layouts.admin')

@section('title', 'Monthly Dining Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dining.index') }}">Dining</a></li>
    <li class="breadcrumb-item active">Monthly Report</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-calendar-alt me-2"></i>Monthly Dining Report</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="month" name="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter"></i> Show</button>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-md-4"><div class="alert alert-warning mb-0"><strong>Total Breakfast:</strong> {{ $totalBreakfast }}</div></div>
            <div class="col-md-4"><div class="alert alert-success mb-0"><strong>Total Lunch:</strong> {{ $totalLunch }}</div></div>
            <div class="col-md-4"><div class="alert alert-info mb-0"><strong>Total Dinner:</strong> {{ $totalDinner }}</div></div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead><tr><th>Day</th><th>Date</th><th>Breakfast</th><th>Lunch</th><th>Dinner</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($dailyStats as $day => $stat)
                    <tr>
                        <td>{{ $day }}</td>
                        <td>{{ $stat['date'] }}</td>
                        <td>{{ $stat['breakfast'] }}</td>
                        <td>{{ $stat['lunch'] }}</td>
                        <td>{{ $stat['dinner'] }}</td>
                        <td><strong>{{ $stat['breakfast'] + $stat['lunch'] + $stat['dinner'] }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
