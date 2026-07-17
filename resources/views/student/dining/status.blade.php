@extends('layouts.student')

@section('title', 'Dining Status')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-utensils me-2"></i>Today's Meal</h5>
            </div>
            <div class="card-body">
                @if($todayMeal)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Meal Status</span>
                        <span class="badge bg-{{ $todayMeal->meal_active ? 'success' : 'danger' }}">{{ $todayMeal->meal_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <form action="{{ route('student.dining.preference') }}" method="POST" class="mb-3">
                        @csrf
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="breakfast" id="breakfast" {{ $todayMeal->breakfast ? 'checked' : '' }}>
                            <label class="form-check-label" for="breakfast">Breakfast</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="lunch" id="lunch" {{ $todayMeal->lunch ? 'checked' : '' }}>
                            <label class="form-check-label" for="lunch">Lunch</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="dinner" id="dinner" {{ $todayMeal->dinner ? 'checked' : '' }}>
                            <label class="form-check-label" for="dinner">Dinner</label>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary d-block mt-2">Update Preferences</button>
                    </form>
                    <form action="{{ route('student.dining.toggle') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-{{ $todayMeal->meal_active ? 'warning' : 'success' }}">
                            <i class="fas fa-{{ $todayMeal->meal_active ? 'pause' : 'play' }} me-1"></i>
                            Turn Meal {{ $todayMeal->meal_active ? 'Off' : 'On' }}
                        </button>
                    </form>
                @else
                    <p class="text-muted">No meal record for today.</p>
                    <form action="{{ route('student.dining.toggle') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="fas fa-play me-1"></i>Activate Meal</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5><i class="fas fa-history me-2"></i>Meal History (Last 30 Days)</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Date</th><th>Breakfast</th><th>Lunch</th><th>Dinner</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($meals as $meal)
                            <tr>
                                <td>{{ $meal->date->format('M d, Y') }}</td>
                                <td><i class="fas fa-{{ $meal->breakfast ? 'check text-success' : 'times text-danger' }}"></i></td>
                                <td><i class="fas fa-{{ $meal->lunch ? 'check text-success' : 'times text-danger' }}"></i></td>
                                <td><i class="fas fa-{{ $meal->dinner ? 'check text-success' : 'times text-danger' }}"></i></td>
                                <td><span class="badge bg-{{ $meal->meal_active ? 'success' : 'danger' }}">{{ $meal->meal_active ? 'On' : 'Off' }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3">No meal history</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
