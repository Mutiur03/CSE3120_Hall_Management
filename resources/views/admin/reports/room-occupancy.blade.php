@extends('layouts.admin')

@section('title', 'Room Occupancy Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Room Occupancy</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-door-open me-2"></i>Room Occupancy Report</h1>
    <div class="btn-group">
        <a href="{{ url()->current() }}?export=pdf" class="btn btn-danger"><i class="fas fa-file-pdf me-1"></i>PDF</a>
        <a href="{{ url()->current() }}?export=excel" class="btn btn-success"><i class="fas fa-file-excel me-1"></i>Excel</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Room</th><th>Floor</th><th>Capacity</th><th>Occupied</th><th>Available</th><th>Occupancy %</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                    <tr>
                        <td>{{ $room->room_no }}</td>
                        <td>{{ $room->floor }}</td>
                        <td>{{ $room->total_seats }}</td>
                        <td>{{ $room->occupied_seats }}</td>
                        <td>{{ $room->available_seats }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $room->occupancy_percentage >= 90 ? 'danger' : ($room->occupancy_percentage >= 50 ? 'warning' : 'success') }}" style="width: {{ $room->occupancy_percentage }}%"></div>
                                </div>
                                <small>{{ $room->occupancy_percentage }}%</small>
                            </div>
                        </td>
                        <td><span class="badge bg-{{ $room->status->value === 'active' ? 'success' : 'warning' }}">{{ ucfirst($room->status->value) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
