@extends('layouts.admin')

@section('title', 'Seats')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Seats</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-bed me-2"></i>Seat Management</h1>
    <a href="{{ route('admin.seats.allocate-form') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Allocate Seat</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="floor" class="form-select">
                    <option value="">All Floors</option>
                    @foreach($floors as $f)
                        <option value="{{ $f }}" {{ request('floor') == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="room_id" class="form-select">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>{{ $r->room_no }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Seat</th><th>Room</th><th>Floor</th><th>Status</th><th>Student</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($seats as $seat)
                    <tr>
                        <td>{{ $seat->id }}</td>
                        <td>{{ $seat->seat_no }}</td>
                        <td>{{ $seat->room->room_no }}</td>
                        <td>{{ $seat->room->floor }}</td>
                        <td>
                            @if($seat->isOccupied())
                                <span class="badge bg-danger">Occupied</span>
                            @elseif($seat->isActive())
                                <span class="badge bg-success">Available</span>
                            @else
                                <span class="badge bg-warning">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $seat->currentAllocation?->student->user->name ?? '-' }}</td>
                        <td>
                            @if($seat->isOccupied())
                                <a href="{{ route('admin.seats.vacate-form', $seat) }}" class="btn btn-sm btn-warning">Vacate</a>
                                <a href="{{ route('admin.seats.transfer-form', $seat) }}" class="btn btn-sm btn-info">Transfer</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4">No seats found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $seats->links() }}</div>
    </div>
</div>
@endsection
