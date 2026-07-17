<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\RoomChangeRequest;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomChangeController extends Controller
{
    public function index()
    {
        $student = auth('student')->user();
        $requests = $student->roomChangeRequests()->latest()->paginate(10);
        return view('student.room-changes.index', compact('requests'));
    }

    public function create()
    {
        $student = auth('student')->user();
        $currentRoom = $student->currentRoom();

        if (!$currentRoom) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You do not have an allocated room.');
        }

        $pendingRequest = $student->roomChangeRequests()->where('status', 'pending')->first();
        if ($pendingRequest) {
            return redirect()->route('student.room-changes.index')
                ->with('error', 'You already have a pending room change request.');
        }

        $availableRooms = Room::where('id', '!=', $currentRoom->id)
            ->where('status', '!=', 'full')
            ->where('gender_type', $student->gender === 'male' ? 'male' : 'female')
            ->get();

        return view('student.room-changes.create', compact('currentRoom', 'availableRooms'));
    }

    public function store(Request $request)
    {
        $student = auth('student')->user();
        $currentRoom = $student->currentRoom();

        if (!$currentRoom) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You do not have an allocated room.');
        }

        $validated = $request->validate([
            'requested_room_id' => 'required|exists:rooms,id',
            'reason' => 'required|string|max:1000',
        ]);

        $validated['student_id'] = $student->id;
        $validated['current_room_id'] = $currentRoom->id;
        $validated['status'] = 'pending';

        RoomChangeRequest::create($validated);

        return redirect()->route('student.room-changes.index')
            ->with('success', 'Room change request submitted successfully.');
    }
}
