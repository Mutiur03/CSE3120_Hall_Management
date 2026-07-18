@extends('layouts.admin')

@section('title', 'Dining Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Dining Report</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-utensils me-2"></i>Monthly Dining Report</h1>
    <div class="btn-group">
        <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger"><i class="fas fa-file-pdf me-1"></i>PDF</a>
        <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success"><i class="fas fa-file-excel me-1"></i>Excel</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="month" name="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter me-1"></i>View</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4"><div class="card text-center"><div class="card-body"><h3>{{ $totalBreakfast }}</h3><p class="mb-0">Total Breakfast</p></div></div></div>
    <div class="col-md-4"><div class="card text-center"><div class="card-body"><h3>{{ $totalLunch }}</h3><p class="mb-0">Total Lunch</p></div></div></div>
    <div class="col-md-4"><div class="card text-center"><div class="card-body"><h3>{{ $totalDinner }}</h3><p class="mb-0">Total Dinner</p></div></div></div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Date</th><th>Breakfast</th><th>Lunch</th><th>Dinner</th></tr>
                </thead>
                <tbody>
                    @foreach($dailyStats as $day => $stat)
                    <tr>
                        <td>{{ $stat['date'] }}</td>
                        <td>{{ $stat['breakfast'] }}</td>
                        <td>{{ $stat['lunch'] }}</td>
                        <td>{{ $stat['dinner'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
