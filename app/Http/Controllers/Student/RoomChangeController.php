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
            ->with('requestedRoom', 'currentSeat.room')
            ->latest()
            ->paginate(10);

        return view('student.room-changes.index', compact('requests', 'student'));
    }

    public function create(): View|RedirectResponse
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $allocation = $student->currentAllocation()->with('seat.room')->first();

        if ($allocation === null) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You must have an allocated seat before requesting a room change.');
        }

        if ($student->roomChangeRequests()->where('status', RoomChangeRequestStatus::Pending)->exists()) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You already have a pending room change request.');
        }

        $currentRoomId = $allocation->seat?->room_id;

        $rooms = Room::query()
            ->where('status', RoomStatus::Active)
            ->when($currentRoomId, fn ($query) => $query->whereKeyNot($currentRoomId))
            ->orderBy('floor')
            ->orderBy('room_no')
            ->get();

        return view('student.room-changes.create', compact('allocation', 'rooms'));
    }

    public function store(StoreRoomChangeRequestRequest $request): RedirectResponse
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $allocation = $student->currentAllocation()->with('seat')->first();

        if ($allocation === null) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You must have an allocated seat before requesting a room change.');
        }

        if ($student->roomChangeRequests()->where('status', RoomChangeRequestStatus::Pending)->exists()) {
            return redirect()
                ->route('student.room-changes.index')
                ->with('error', 'You already have a pending room change request.');
        }

        if ((int) $request->validated('requested_room_id') === (int) $allocation->seat?->room_id) {
            return redirect()
                ->route('student.room-changes.create')
                ->withInput()
                ->with('error', 'The requested room is your current room. Choose a different room.');
        }

        RoomChangeRequest::create([
            'student_id' => $student->id,
            'current_seat_id' => $allocation->seat_id,
            'requested_room_id' => $request->validated('requested_room_id'),
            'reason' => $request->validated('reason'),
            'status' => RoomChangeRequestStatus::Pending,
        ]);

        return redirect()
            ->route('student.room-changes.index')
            ->with('success', 'Room change request submitted successfully.');
    }
}
