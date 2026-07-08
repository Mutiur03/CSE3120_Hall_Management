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
            
            
            
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Seat</th><th>Room</th><th>Building</th><th>Floor</th><th>Status</th><th>Student</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($seats as $seat)
                    <tr>
                        <td>{{ $seat->id }}</td>
                        <td>{{ $seat->seat_number }}</td>
                        <td>{{ $seat->room->room_number }}</td>
                        <td>{{ $seat->room->building }}</td>
                        <td>{{ $seat->room->floor }}</td>
                        <td><span class="badge bg-{{ $seat->status === 'available' ? 'success' : ($seat->status === 'occupied' ? 'danger' : 'warning') }}">{{ ucfirst($seat->status) }}</span></td>
                        <td>{{ $seat->currentAllocation?->student->name ?? '-' }}</td>
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
