@extends('layouts.admin')

@section('title', 'Allocate Seat')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.seats.index') }}">Seats</a></li>
    <li class="breadcrumb-item active">Allocate</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-plus me-2"></i>Allocate Seat to Student</h1>
    <a href="{{ route('admin.seats.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.seats.allocate') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Select Student <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">Choose Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->student_id }} - {{ $student->name }} ({{ $student->department }})</option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Select Seat <span class="text-danger">*</span></label>
                <select name="seat_id" class="form-select @error('seat_id') is-invalid @enderror" required>
                    <option value="">Choose Seat</option>
                    @foreach($availableSeats as $seat)
                        <option value="{{ $seat->id }}">{{ $seat->room->building }} - Room {{ $seat->room->room_number }} - {{ $seat->seat_number }} (Floor {{ $seat->room->floor }})</option>
                    @endforeach
                </select>
                @error('seat_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.seats.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Allocate Seat</button>
            </div>
        </form>
    </div>
</div>
@endsection
