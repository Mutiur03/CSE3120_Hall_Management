@extends('layouts.admin')

@section('title', 'Dining Attendance')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dining.index') }}">Dining</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clipboard-check me-2"></i>Dining Attendance</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <select name="meal_type" class="form-select">
                    <option value="breakfast" {{ $mealType == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                    <option value="lunch" {{ $mealType == 'lunch' ? 'selected' : '' }}>Lunch</option>
                    <option value="dinner" {{ $mealType == 'dinner' ? 'selected' : '' }}>Dinner</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter"></i> Show</button>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert alert-success mb-0"><strong>Present:</strong> {{ $presentCount }}</div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-danger mb-0"><strong>Absent:</strong> {{ $absentCount }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>Student</th><th>Roll</th><th>Status</th><th>Time</th></tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr>
                        <td>{{ $att->student->user->name }}</td>
                        <td>{{ $att->student->roll }}</td>
                        <td><span class="badge bg-{{ $att->present ? 'success' : 'danger' }}">{{ $att->present ? 'Present' : 'Absent' }}</span></td>
                        <td>{{ $att->time ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4">No attendance records</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">{{ $attendances->links() }}</div>
    </div>
</div>
@endsection
