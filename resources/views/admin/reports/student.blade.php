@extends('layouts.admin')

@section('title', 'Student Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Student Report</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-users me-2"></i>Student Report</h1>
    <div class="btn-group">
        <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger"><i class="fas fa-file-pdf me-1"></i>PDF</a>
        <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success"><i class="fas fa-file-excel me-1"></i>Excel</a>
        <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-secondary"><i class="fas fa-file-csv me-1"></i>CSV</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="session" class="form-select">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $sess)
                        <option value="{{ $sess }}" {{ request('session') == $sess ? 'selected' : '' }}>{{ $sess }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>Student ID</th><th>Name</th><th>Department</th><th>Session</th><th>Batch</th><th>Gender</th><th>Phone</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->department }}</td>
                        <td>{{ $student->session }}</td>
                        <td>{{ $student->batch }}</td>
                        <td>{{ ucfirst($student->gender) }}</td>
                        <td>{{ $student->phone }}</td>
                        <td><span class="badge bg-{{ $student->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($student->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
