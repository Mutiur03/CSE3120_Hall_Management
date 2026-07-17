@extends('layouts.admin')

@section('title', 'Room Change Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.room-changes.index') }}">Room Changes</a></li>
    <li class="breadcrumb-item active">#{{ $roomChange->id }}</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-exchange-alt me-2"></i>Room Change Request #{{ $roomChange->id }}</h1>
    <a href="{{ route('admin.room-changes.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>Request Details</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Student</th><td>{{ $roomChange->student->name }} ({{ $roomChange->student->student_id }})</td></tr>
                    <tr><th>Current Room</th><td>{{ $roomChange->currentRoom->room_number }} ({{ $roomChange->currentRoom->building }})</td></tr>
                    <tr><th>Requested Room</th><td>{{ $roomChange->requestedRoom->room_number }} ({{ $roomChange->requestedRoom->building }})</td></tr>
                    <tr><th>Reason</th><td>{{ $roomChange->reason }}</td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $roomChange->status === 'pending' ? 'warning' : ($roomChange->status === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($roomChange->status) }}</span></td></tr>
                    <tr><th>Submitted</th><td>{{ $roomChange->created_at->format('M d, Y H:i') }}</td></tr>
                    @if($roomChange->admin_remarks)
                    <tr><th>Admin Remarks</th><td>{{ $roomChange->admin_remarks }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @if($roomChange->status === 'pending')
    <div class="col-lg-6">
        <div class="card border-success">
            <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-check me-2"></i>Approve Request</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.room-changes.approve', $roomChange) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Remarks (optional)</label>
                        <textarea name="admin_remarks" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this room change request?')">
                        <i class="fas fa-check me-1"></i>Approve & Transfer
                    </button>
                </form>
            </div>
        </div>
        <div class="card border-danger mt-3">
            <div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fas fa-times me-2"></i>Reject Request</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.room-changes.reject', $roomChange) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="admin_remarks" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100"><i class="fas fa-times me-1"></i>Reject Request</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
