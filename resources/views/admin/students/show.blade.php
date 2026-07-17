@extends('layouts.admin')

@section('title', 'Student Profile')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">{{ $student->name }}</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user me-2"></i>Student Profile</h1>
    <div class="btn-group">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i>Edit</a>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="" class="rounded-circle mb-3" width="120" height="120">
                @else
                    <div class="avatar-placeholder mx-auto mb-3" style="width:120px;height:120px;font-size:48px;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
                <h4>{{ $student->name }}</h4>
                <p class="text-muted">{{ $student->student_id }}</p>
                <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'warning' }} mb-2">{{ ucfirst($student->status) }}</span>
                <div class="mt-3">
                    <p><i class="fas fa-envelope me-2"></i>{{ $student->email }}</p>
                    <p><i class="fas fa-phone me-2"></i>{{ $student->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5>Current Allocation</h5></div>
            <div class="card-body">
                @if($student->currentAllocation)
                    <p><strong>Room:</strong> {{ $student->currentAllocation->room->room_number }}</p>
                    <p><strong>Seat:</strong> {{ $student->currentAllocation->seat->seat_number }}</p>
                    <p><strong>Building:</strong> {{ $student->currentAllocation->room->building }}</p>
                    <p><strong>Since:</strong> {{ $student->currentAllocation->allocation_date->format('M d, Y') }}</p>
                @else
                    <p class="text-muted">No active seat allocation</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5>Personal Information</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="30%">Department</th><td>{{ $student->department }}</td></tr>
                    <tr><th>Session</th><td>{{ $student->session }}</td></tr>
                    <tr><th>Batch</th><td>{{ $student->batch }}</td></tr>
                    <tr><th>Gender</th><td>{{ ucfirst($student->gender) }}</td></tr>
                    <tr><th>Blood Group</th><td>{{ $student->blood_group ?? 'N/A' }}</td></tr>
                    <tr><th>Address</th><td>{{ $student->address ?? 'N/A' }}</td></tr>
                    <tr><th>Guardian Name</th><td>{{ $student->guardian_name ?? 'N/A' }}</td></tr>
                    <tr><th>Guardian Phone</th><td>{{ $student->guardian_phone ?? 'N/A' }}</td></tr>
                    <tr><th>Password Changed</th><td><span class="badge bg-{{ $student->password_changed ? 'success' : 'warning' }}">{{ $student->password_changed ? 'Yes' : 'No' }}</span></td></tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5>Allocation History</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Room</th><th>Seat</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($student->seatAllocations as $alloc)
                            <tr>
                                <td>{{ $alloc->room->room_number }}</td>
                                <td>{{ $alloc->seat->seat_number }}</td>
                                <td>{{ $alloc->allocation_date->format('M d, Y') }}</td>
                                <td><span class="badge bg-{{ $alloc->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($alloc->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3">No allocation history</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
