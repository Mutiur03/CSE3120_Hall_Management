@extends('layouts.admin')

@section('title', 'Occupied Seats')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.seats.index') }}">Seats</a></li>
    <li class="breadcrumb-item active">Occupied</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-check-circle text-danger me-2"></i>Occupied Seats</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="building" class="form-select">
                    <option value="">All Buildings</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b }}" {{ request('building') == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Seat</th><th>Room</th><th>Building</th><th>Student</th><th>Student ID</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($seats as $seat)
                    <tr>
                        <td><span class="badge bg-danger">{{ $seat->seat_number }}</span></td>
                        <td>{{ $seat->room->room_number }}</td>
                        <td>{{ $seat->room->building }}</td>
                        <td>{{ $seat->currentAllocation?->student->name ?? '-' }}</td>
                        <td>{{ $seat->currentAllocation?->student->student_id ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.seats.vacate-form', $seat) }}" class="btn btn-sm btn-warning">Vacate</a>
                            <a href="{{ route('admin.seats.transfer-form', $seat) }}" class="btn btn-sm btn-info">Transfer</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No occupied seats found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $seats->links() }}</div>
    </div>
</div>
@endsection
