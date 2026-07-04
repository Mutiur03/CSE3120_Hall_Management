@extends('layouts.admin')

@section('title', 'Rooms')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Rooms</li>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-door-open me-2"></i>Room Management</h1>
    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Create Room
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            @forelse($rooms as $room)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card room-card h-100">
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('admin.rooms.show', $room) }}" class="btn btn-sm btn-info"><i class="fas fa-eye me-1"></i>View</a>
                        <div class="btn-group">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm(' this room?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-4">No rooms found</div>
            @endforelse
        </div>

        <div class="d-flex justify-content-end">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
@endsection
