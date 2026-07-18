<?php

namespace App\Http\Controllers\Student;

use App\Enums\RoomChangeRequestStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreRoomChangeRequestRequest;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomChangeController extends Controller
{
    public function index(): View
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $requests = $student->roomChangeRequests()
            ->with('requestedRoom')
            ->latest()
            ->paginate(10);

        return view('student.room-changes.index', compact('requests', 'student'));
    }

    public function show(RoomChangeRequest $roomChange): View
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);
        abort_unless($roomChange->student_id === $student->id, 403);

        $roomChange->load(['currentSeat.room', 'requestedRoom', 'reviewer']);

        return view('student.room-changes.show', compact('roomChange', 'student'));
    }

    public function create(): View|RedirectResponse
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $currentRoom = $student->currentRoom();

        if ($currentRoom === null) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You do not have an allocated room.');
        }

        if ($student->roomChangeRequests()->where('status', RoomChangeRequestStatus::Pending)->exists()) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You already have a pending room change request.');
        }

        $availableRooms = Room::query()
            ->where('id', '!=', $currentRoom->id)
            ->where('status', RoomStatus::Active)
            ->orderBy('floor')
            ->orderBy('room_no')
            ->get();

        return view('student.room-changes.create', compact('currentRoom', 'availableRooms'));
    }

    public function store(StoreRoomChangeRequestRequest $request): RedirectResponse
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $allocation = $student->currentAllocation;

        if ($allocation === null) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You do not have an allocated room.');
        }

        if ($student->roomChangeRequests()->where('status', RoomChangeRequestStatus::Pending)->exists()) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You already have a pending room change request.');
        }

        $currentRoom = $student->currentRoom();

        if ($currentRoom !== null && (int) $request->validated('requested_room_id') === $currentRoom->id) {
            return redirect()
                ->route('student.room-changes.create')
                ->with('error', 'You cannot request your current room.');
        }

        RoomChangeRequest::create([
            ...$request->validated(),
            'student_id' => $student->id,
            'current_seat_id' => $allocation->seat_id,
            'status' => RoomChangeRequestStatus::Pending,
        ]);

        return redirect()
            ->route('student.room-changes.index')
            ->with('success', 'Room change request submitted successfully.');
    }
}
