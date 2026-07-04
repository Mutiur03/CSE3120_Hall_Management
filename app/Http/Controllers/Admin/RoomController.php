<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Seat;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function destroy(Room $room)
    {
        $room->seats()->delete();
        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
