@extends('layouts.admin')

@section('title', 'Change Password')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Change Password</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-lock me-2"></i>Change Password</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.change-password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="new_password">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" required autocomplete="new-password">
                        @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="new_password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save New Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
