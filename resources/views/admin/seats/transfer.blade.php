@extends('layouts.admin')

@section('title', 'Transfer Seat')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.seats.index') }}">Seats</a></li>
    <li class="breadcrumb-item active">Transfer</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-exchange-alt me-2"></i>Transfer Student</h1>
    <a href="{{ route('admin.seats.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            <h5>Current Seat Details</h5>
            <p class="mb-1"><strong>Room:</strong> {{ $seat->room->room_no }} (Floor {{ $seat->room->floor }})</p>
            <p class="mb-1"><strong>Seat:</strong> {{ $seat->seat_no }}</p>
            <p class="mb-0"><strong>Student:</strong> {{ $seat->currentAllocation?->student?->user?->name ?? 'N/A' }}</p>
        </div>

        <form action="{{ route('admin.seats.transfer', $seat) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Select New Seat <span class="text-danger">*</span></label>
                <select name="target_seat_id" class="form-select @error('target_seat_id') is-invalid @enderror" required>
                    <option value="">Choose New Seat</option>
                    @foreach($targetSeats as $availSeat)
                        <option value="{{ $availSeat->id }}">Room {{ $availSeat->room->room_no }} - Seat {{ $availSeat->seat_no }} (Floor {{ $availSeat->room->floor }})</option>
                    @endforeach
                </select>
                @error('target_seat_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.seats.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-exchange-alt me-1"></i>Transfer Student</button>
            </div>
        </form>
    </div>
</div>
@endsection
