@extends('layouts.admin')

@section('title', 'Settings')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-cog me-2"></i>System Settings</h1>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>General Settings</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Application Name</label>
                        <input type="text" name="app_name" class="form-control" value="{{ config('app.name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Timezone</label>
                        <input type="text" class="form-control" value="{{ config('app.timezone') }}" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>System Maintenance</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.settings.clear-cache') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Clear Cache</label>
                        <p class="text-muted small">Clear application cache, config cache, and view cache.</p>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-broom me-1"></i>Clear Cache</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
