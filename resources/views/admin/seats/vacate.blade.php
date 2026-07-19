@extends('layouts.admin')

@section('title', 'Vacate Seat')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.seats.index') }}">Seats</a></li>
    <li class="breadcrumb-item active">Vacate</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-sign-out-alt me-2"></i>Vacate Seat</h1>
    <a href="{{ route('admin.seats.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-warning">
            <h5>Seat Details</h5>
            <p class="mb-1"><strong>Room:</strong> {{ $seat->room->room_no }} (Floor {{ $seat->room->floor }})</p>
            <p class="mb-1"><strong>Seat:</strong> {{ $seat->seat_no }}</p>
            <p class="mb-1"><strong>Student:</strong> {{ $seat->currentAllocation?->student?->user?->name ?? 'N/A' }}</p>
            <p class="mb-0"><strong>Allocated Since:</strong> {{ $seat->currentAllocation?->allocated_at?->format('M d, Y') ?? 'N/A' }}</p>
        </div>

        <form action="{{ route('admin.seats.vacate', $seat) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.seats.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to vacate this seat?')">
                    <i class="fas fa-sign-out-alt me-1"></i>Vacate Seat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
