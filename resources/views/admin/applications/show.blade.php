@extends('layouts.admin')

@section('title', 'Application Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a></li>
    <li class="breadcrumb-item active">#{{ $application->id }}</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-file-alt me-2"></i>Application #{{ $application->id }}</h1>
    <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>Application Details</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Student</th><td>{{ $application->student->user->name }} ({{ $application->student->roll }})</td></tr>
                    <tr><th>Department</th><td>{{ $application->student->department }}</td></tr>
                    <tr><th>Preferred Floor</th><td>{{ $application->preferred_floor ?? 'Any' }}</td></tr>
                    <tr><th>Preferred Room</th><td>{{ $application->preferredRoom?->room_no ?? 'Any' }}</td></tr>
                    <tr><th>Reason</th><td>{{ $application->reason ?? 'N/A' }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $application->status->value === 'pending' ? 'warning' : ($application->status->value === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($application->status->value) }}</span></td></tr>
                    <tr><th>Submitted</th><td>{{ $application->created_at->format('M d, Y H:i') }}</td></tr>
                    @if($application->admin_comment)
                    <tr><th>Admin Comment</th><td>{{ $application->admin_comment }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @if($application->status->value === 'pending')
    <div class="col-lg-6">
        <div class="card border-success">
            <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-check me-2"></i>Approve Application</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Comment (optional)</label>
                        <textarea name="admin_comment" class="form-control @error('admin_comment') is-invalid @enderror" rows="2"></textarea>
                        @error('admin_comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-1"></i>Approve Application</button>
                </form>
            </div>
        </div>

        <div class="card border-danger mt-3">
            <div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fas fa-times me-2"></i>Reject Application</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.applications.reject', $application) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="admin_comment" class="form-control @error('admin_comment') is-invalid @enderror" rows="3" required></textarea>
                        @error('admin_comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-danger w-100"><i class="fas fa-times me-1"></i>Reject Application</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
