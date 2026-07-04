@extends('layouts.student')

@section('title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="" class="rounded-circle mb-3" width="120" height="120">
                @else
                    <div class="avatar-placeholder mx-auto mb-3" style="width:120px;height:120px;font-size:48px;line-height:120px;background:#2563eb;color:#fff;border-radius:50%;">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
                <h4>{{ $student->name }}</h4>
                <p class="text-muted">{{ $student->student_id }}</p>
                <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($student->status) }}</span>
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
                    <tr><th>Phone</th><td>{{ $student->phone ?? 'N/A' }}</td></tr>
                    <tr><th>Email</th><td>{{ $student->email }}</td></tr>
                    <tr><th>Address</th><td>{{ $student->address ?? 'N/A' }}</td></tr>
                    <tr><th>Guardian Name</th><td>{{ $student->guardian_name ?? 'N/A' }}</td></tr>
                    <tr><th>Guardian Phone</th><td>{{ $student->guardian_phone ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
