<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AllocationStatus;
use App\Enums\RoomChangeRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatAllocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoomChangeController extends Controller
{
    public function index(Request $request): View
    {
        $query = RoomChangeRequest::query()
            ->with(['student.user', 'currentSeat.room', 'requestedRoom']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->value());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->whereHas('student', function ($q) use ($search): void {
                $q->where('roll', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('admin.room-changes.index', compact('requests'));
    }

    public function show(RoomChangeRequest $roomChange): View
    {
        $roomChange->load(['student.user', 'currentSeat.room', 'requestedRoom', 'reviewer']);

        return view('admin.room-changes.show', compact('roomChange'));
    }

    public function approve(Request $request, RoomChangeRequest $roomChange): RedirectResponse
    {
        if ($roomChange->status !== RoomChangeRequestStatus::Pending) {
            return redirect()->back()->with('error', 'This request is not pending.');
        }

        $validated = $request->validate([
            'target_seat_id' => ['required', 'integer', 'exists:seats,id'],
            'admin_comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $targetSeat = Seat::query()->with('currentAllocation')->findOrFail($validated['target_seat_id']);

        if ($targetSeat->room_id !== $roomChange->requested_room_id) {
            throw ValidationException::withMessages([
                'target_seat_id' => 'The selected seat is not in the requested room.',
            ]);
        }

        if ($targetSeat->isOccupied()) {
            throw ValidationException::withMessages([
                'target_seat_id' => 'The selected seat is already occupied.',
            ]);
        }

        DB::transaction(function () use ($roomChange, $validated): void {
            $student = $roomChange->student;

            $targetSeat = Seat::query()->lockForUpdate()->findOrFail($validated['target_seat_id']);

            if (SeatAllocation::query()
                ->where('seat_id', $targetSeat->id)
                ->where('status', AllocationStatus::Active)
                ->lockForUpdate()
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'target_seat_id' => 'The selected seat is already occupied.',
                ]);
            }

            $currentAllocation = SeatAllocation::query()
                ->where('student_id', $student->id)
                ->where('status', AllocationStatus::Active)
                ->lockForUpdate()
                ->first();

            if ($currentAllocation) {
                $currentAllocation->update([
                    'status' => AllocationStatus::Vacated,
                    'vacated_at' => now()->toDateString(),
                ]);
            }

            SeatAllocation::create([
                'student_id' => $student->id,
                'seat_id' => $targetSeat->id,
                'allocated_by' => auth()->id(),
                'allocated_at' => now()->toDateString(),
                'status' => AllocationStatus::Active,
            ]);

            $roomChange->update([
                'status' => RoomChangeRequestStatus::Approved,
                'admin_comment' => $validated['admin_comment'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.room-changes.index')
            ->with('success', 'Room change request approved.');
    }

    public function reject(Request $request, RoomChangeRequest $roomChange): RedirectResponse
    {
        if ($roomChange->status !== RoomChangeRequestStatus::Pending) {
            return redirect()->back()->with('error', 'This request is not pending.');
        }

        $validated = $request->validate([
            'admin_comment' => ['required', 'string', 'max:1000'],
        ]);

        $roomChange->update([
            'status' => RoomChangeRequestStatus::Rejected,
            'admin_comment' => $validated['admin_comment'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.room-changes.index')
            ->with('success', 'Room change request rejected.');
    }
}
