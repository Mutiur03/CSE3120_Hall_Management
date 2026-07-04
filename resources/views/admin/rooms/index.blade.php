@extends('layouts.admin')

@section('title', 'Rooms | Hall Management System')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Rooms</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Room No</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Floor</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Capacity</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Occupied</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($rooms as $room)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $room->room_no }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $room->floor }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $room->capacity }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $room->occupied_seats_count }}/{{ $room->capacity }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $room->status->value === 'active' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($room->status->value) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.rooms.show', $room) }}" class="text-blue-600 hover:text-blue-800 font-medium">View Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No rooms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($rooms->hasPages())
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $rooms->links() }}
        </div>
    @endif
</div>
@endsection
