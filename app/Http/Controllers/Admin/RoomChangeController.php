<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomChangeController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomChangeRequest::with(['student', 'currentRoom', 'requestedRoom']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('admin.room-changes.index', compact('requests'));
    }

    public function show(RoomChangeRequest $roomChange)
    {
        $roomChange->load(['student', 'currentRoom', 'requestedRoom']);
        return view('admin.room-changes.show', compact('roomChange'));
    }

    public function approve(Request $request, RoomChangeRequest $roomChange)
    {
        if ($roomChange->status !== 'pending') {
            return redirect()->back()->with('error', 'Request is not pending.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($roomChange, $validated) {
            $student = $roomChange->student;
            $currentAllocation = $student->currentAllocation;
            $requestedRoom = $roomChange->requestedRoom;

            // Find available seat in requested room
            $availableSeat = Seat::where('room_id', $requestedRoom->id)
                ->where('status', 'available')
                ->first();

            if (!$availableSeat) {
                throw new \Exception('No available seats in requested room.');
            }

            // Vacate current seat
            if ($currentAllocation) {
                $currentAllocation->update([
                    'status' => 'vacated',
                    'vacate_date' => now(),
                    'notes' => 'Room change approved',
                ]);

                $oldSeat = $currentAllocation->seat;
                $oldSeat->update(['status' => 'available']);

                $oldRoom = $oldSeat->room;
                if ($oldRoom->status === 'full') {
                    $oldRoom->update(['status' => 'available']);
                }
            }

            // Create new allocation
            SeatAllocation::create([
                'student_id' => $student->id,
                'seat_id' => $availableSeat->id,
                'room_id' => $requestedRoom->id,
                'allocation_date' => now(),
                'status' => 'active',
            ]);

            $availableSeat->update(['status' => 'occupied']);

            if ($requestedRoom->isFull()) {
                $requestedRoom->update(['status' => 'full']);
            }

            $roomChange->update([
                'status' => 'approved',
                'admin_remarks' => $validated['admin_remarks'] ?? null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('admin.room-changes.index')
            ->with('success', 'Room change request approved.');
    }

    public function reject(Request $request, RoomChangeRequest $roomChange)
    {
        if ($roomChange->status !== 'pending') {
            return redirect()->back()->with('error', 'Request is not pending.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string',
        ]);

        $roomChange->update([
            'status' => 'rejected',
            'admin_remarks' => $validated['admin_remarks'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.room-changes.index')
            ->with('success', 'Room change request rejected.');
    }
}
