<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Room;
use App\Models\Student;
use App\Models\SeatAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeatController extends Controller
{
    

    public function available(Request $request)
    {
        $query = Seat::with('room')->where('status', 'available');

        if ($request->filled('building')) {
            $query->whereHas('room', function ($q) use ($request) {
                $q->where('building', $request->building);
            });
        }

        if ($request->filled('floor')) {
            $query->whereHas('room', function ($q) use ($request) {
                $q->where('floor', $request->floor);
            });
        }

        $seats = $query->paginate(50)->withQueryString();
        $buildings = Room::select('building')->distinct()->pluck('building');
        $floors = Room::select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('admin.seats.available', compact('seats', 'buildings', 'floors'));
    }

   
}
