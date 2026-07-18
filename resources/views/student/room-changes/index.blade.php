@extends('layouts.student')

@section('title', 'Room Change Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-exchange-alt me-2"></i>My Room Change Requests</h2>
    @if(auth()->user()->student?->currentRoom())
        <a href="{{ route('student.room-changes.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Request</a>
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>ID</th><th>Current Room</th><th>Requested Room</th><th>Reason</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ $req->currentSeat?->room?->room_no ?? '—' }}</td>
                        <td>{{ $req->requestedRoom?->room_no ?? '—' }}</td>
                        <td>{{ Str::limit($req->reason, 40) }}</td>
                        <td><span class="badge bg-{{ $req->status->value === 'pending' ? 'warning' : ($req->status->value === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($req->status->value) }}</span></td>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No requests found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">{{ $requests->links() }}</div>
</div>
@endsection
