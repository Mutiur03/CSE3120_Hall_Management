@extends('layouts.admin')

@section('title', 'Dining Management')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Dining</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-utensils me-2"></i>Dining Management</h1>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <i class="fas fa-coffee fa-2x mb-2"></i>
                <h4>{{ $breakfastCount }}</h4>
                <p>Breakfast</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-hamburger fa-2x mb-2"></i>
                <h4>{{ $lunchCount }}</h4>
                <p>Lunch</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-utensils fa-2x mb-2"></i>
                <h4>{{ $dinnerCount }}</h4>
                <p>Dinner</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Meal Records - {{ $date->format('M d, Y') }}</h5>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Student</th><th>Breakfast</th><th>Lunch</th><th>Dinner</th><th>Meal Active</th></tr>
                </thead>
                <tbody>
                    @forelse($meals as $meal)
                    <tr>
                        <td>{{ $meal->student->user->name }}<br><small>{{ $meal->student->roll }}</small></td>
                        <td><i class="fas fa-{{ $meal->breakfast ? 'check text-success' : 'times text-danger' }}"></i></td>
                        <td><i class="fas fa-{{ $meal->lunch ? 'check text-success' : 'times text-danger' }}"></i></td>
                        <td><i class="fas fa-{{ $meal->dinner ? 'check text-success' : 'times text-danger' }}"></i></td>
                        <td><span class="badge bg-{{ $meal->meal_active ? 'success' : 'danger' }}">{{ $meal->meal_active ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">No meal records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">{{ $meals->links() }}</div>
</div>
@endsection
