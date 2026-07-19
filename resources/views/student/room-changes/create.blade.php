@extends('layouts.student')

@section('title', 'Request Room Change')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-exchange-alt me-2"></i>Room Change Request</h5></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <p class="mb-1"><strong>Current Room:</strong> {{ $currentRoom->room_no }} (Floor {{ $currentRoom->floor }})</p>
                </div>
                <form action="{{ route('student.room-changes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Requested Room <span class="text-danger">*</span></label>
                        <select name="requested_room_id" class="form-select" required>
                            <option value="">Select Room</option>
                            @foreach($availableRooms as $room)
                                <option value="{{ $room->id }}">Room {{ $room->room_no }} (Floor {{ $room->floor }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
