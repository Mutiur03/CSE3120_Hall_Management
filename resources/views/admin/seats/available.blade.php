@extends('layouts.admin')

@section('title', 'Available Seats')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.seats.index') }}">Seats</a></li>
    <li class="breadcrumb-item active">Available</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-circle text-success me-2"></i>Available Seats</h1>
    <a href="{{ route('admin.seats.allocate-form') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Allocate Seat</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="floor" class="form-select">
                    <option value="">All Floors</option>
                    @foreach($floors as $f)
                        <option value="{{ $f }}" {{ request('floor') == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Seat</th><th>Room</th><th>Floor</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($seats as $seat)
                    <tr>
                        <td><span class="badge bg-success">{{ $seat->seat_no }}</span></td>
                        <td>{{ $seat->room->room_no }}</td>
                        <td>{{ $seat->room->floor }}</td>
                        <td>
                            <form action="{{ route('admin.seats.allocate') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="seat_id" value="{{ $seat->id }}">
                                <select name="student_id" class="form-select form-select-sm d-inline w-auto" required>
                                    <option value="">Select Student</option>
                                    @foreach($unallocatedStudents as $student)
                                        <option value="{{ $student->id }}">{{ $student->roll }} - {{ $student->user->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">Allocate</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">No available seats found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $seats->links() }}</div>
    </div>
</div>
@endsection
