@extends('layouts.admin')

@section('title', 'Seat Statistics | Hall Management System')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Seat Statistics</h1>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-sm text-slate-500">Total Seats</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ $totalSeats }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-sm text-slate-500">Occupied</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $occupiedSeats }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-sm text-slate-500">Available</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ $availableSeats }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-sm text-slate-500">Inactive</p>
        <p class="text-2xl font-bold text-slate-600 mt-1">{{ $inactiveSeats }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
    <h2 class="font-semibold text-slate-800 mb-3">Overall Occupancy</h2>
    <div class="h-4 rounded-full bg-slate-100 overflow-hidden">
        <div class="h-full bg-red-500" style="width: {{ $occupancyPercentage }}%"></div>
    </div>
    <p class="text-sm text-slate-600 mt-2">{{ $occupancyPercentage }}% occupied</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200">
        <h2 class="font-semibold text-slate-800">Floor-wise Statistics</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Floor</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Total</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Occupied</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Available</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($floorStats as $stat)
                    <tr>
                        <td class="px-4 py-3 text-slate-800">Floor {{ $stat->floor }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $stat->total_seats }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $stat->occupied_seats }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $stat->available_seats }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $stat->occupancy_rate }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">No floor data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
