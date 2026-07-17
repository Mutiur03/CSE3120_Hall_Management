@extends('layouts.admin')

@section('title', 'Room Change Requests')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Room Changes</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-exchange-alt me-2"></i>Room Change Requests</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by student..." value="{{ request('search') }}">
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
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Student</th><th>Current Room</th><th>Requested Room</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ $req->student->name }}<br><small>{{ $req->student->student_id }}</small></td>
                        <td>{{ $req->currentRoom->room_number }}</td>
                        <td>{{ $req->requestedRoom->room_number }}</td>
                        <td>{{ Str::limit($req->reason, 40) }}</td>
                        <td><span class="badge bg-{{ $req->status === 'pending' ? 'warning' : ($req->status === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($req->status) }}</span></td>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                        <td><a href="{{ route('admin.room-changes.show', $req) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4">No requests found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
