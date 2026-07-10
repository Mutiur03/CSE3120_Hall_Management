@extends('layouts.admin')

@section('title', 'Reports')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-chart-bar me-2"></i>Reports</h1>
</div>

<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card report-card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h5>Student Report</h5>
                <p class="text-muted">View and export student data</p>
                <a href="{{ route('admin.reports.students') }}" class="btn btn-primary">View Report</a>
            </div>
        </div>
    </div>
    
    
</div>
@endsection
