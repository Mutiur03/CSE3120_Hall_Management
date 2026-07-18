@extends('layouts.admin')

@section('title', 'Applications')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Applications</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-file-alt me-2"></i>Seat Applications</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by student ID or name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Student</th><th>Department</th><th>Preferred</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <td>{{ $app->id }}</td>
                        <td>{{ $app->student->name }}<br><small>{{ $app->student->student_id }}</small></td>
                        <td>{{ $app->student->department }}</td>
                        <td>{{ $app->preferred_building ?? 'Any' }}<br>{{ $app->preferred_room ?? '' }}</td>
                        <td>{{ Str::limit($app->reason, 50) }}</td>
                        <td><span class="badge bg-{{ $app->status->value === 'pending' ? 'warning' : ($app->status->value === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($app->status->value) }}</span></td>
                        <td>{{ $app->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4">No applications found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $applications->links() }}</div>
    </div>
</div>
@endsection
