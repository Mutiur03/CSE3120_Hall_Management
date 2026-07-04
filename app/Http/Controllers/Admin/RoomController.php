<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::query()
            ->withCount([
                'seats as occupied_seats_count' => function ($query): void {
                    $query->whereHas('currentAllocation');
                },
            ])
            ->orderBy('room_no')
            ->paginate(20);

        return view('admin.rooms.index', compact('rooms'));
    }

    public function show(Room $room): View
    {
        $room->load([
            'seats' => fn ($query) => $query->orderBy('seat_no'),
            'seats.currentAllocation.student.user',
        ]);

        return view('admin.rooms.show', compact('room'));
    }
}
