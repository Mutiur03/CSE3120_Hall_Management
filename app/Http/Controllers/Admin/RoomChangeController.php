<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AllocationStatus;
use App\Enums\RoomChangeRequestStatus;
use App\Enums\SeatStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveRoomChangeRequest;
use App\Http\Requests\Admin\RejectRoomChangeRequest;
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
        $query = RoomChangeRequest::with([
            'student.user',
            'currentSeat.room',
            'requestedRoom.seats.currentAllocation',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->whereHas('student', function ($studentQuery) use ($search) {
                $studentQuery
                    ->where('roll', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('admin.room-changes.index', compact('requests'));
    }

    public function approve(ApproveRoomChangeRequest $request, RoomChangeRequest $roomChange): RedirectResponse
    {
        if (! $roomChange->isPending()) {
            return redirect()->back()->with('error', 'Room change request is not pending.');
        }

        try {
            DB::transaction(function () use ($request, $roomChange): void {
                $lockedRequest = RoomChangeRequest::query()->lockForUpdate()->findOrFail($roomChange->id);

                if ($lockedRequest->status !== RoomChangeRequestStatus::Pending) {
                    throw ValidationException::withMessages([
                        'status' => 'Room change request is not pending.',
                    ]);
                }

                $targetSeat = Seat::query()
                    ->lockForUpdate()
                    ->findOrFail($request->validated('target_seat_id'));

                if ($targetSeat->room_id !== $lockedRequest->requested_room_id) {
                    throw ValidationException::withMessages([
                        'target_seat_id' => 'The selected seat does not belong to the requested room.',
                    ]);
                }

                if ($targetSeat->status !== SeatStatus::Active) {
                    throw ValidationException::withMessages([
                        'target_seat_id' => 'The selected seat is not available for allocation.',
                    ]);
                }

                if ($targetSeat->currentAllocation()->exists()) {
                    throw ValidationException::withMessages([
                        'target_seat_id' => 'The selected seat is already occupied.',
                    ]);
                }

                $currentAllocation = SeatAllocation::query()
                    ->where('student_id', $lockedRequest->student_id)
                    ->where('status', AllocationStatus::Active)
                    ->lockForUpdate()
                    ->first();

                if (! $currentAllocation) {
                    throw ValidationException::withMessages([
                        'status' => 'The student no longer has an active seat allocation.',
                    ]);
                }

                $currentAllocation->update([
                    'status' => AllocationStatus::Vacated,
                    'vacated_at' => now()->toDateString(),
                ]);

                SeatAllocation::create([
                    'student_id' => $lockedRequest->student_id,
                    'seat_id' => $targetSeat->id,
                    'allocated_by' => auth()->id(),
                    'allocated_at' => now()->toDateString(),
                    'status' => AllocationStatus::Active,
                ]);

                $lockedRequest->update([
                    'status' => RoomChangeRequestStatus::Approved,
                    'admin_comment' => $request->validated('admin_comment'),
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);
            });
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()
            ->route('admin.room-changes.index')
            ->with('success', 'Room change approved and seat transferred.');
    }

    public function reject(RejectRoomChangeRequest $request, RoomChangeRequest $roomChange): RedirectResponse
    {
        if (! $roomChange->isPending()) {
            return redirect()->back()->with('error', 'Room change request is not pending.');
        }

        $roomChange->update([
            'status' => RoomChangeRequestStatus::Rejected,
            'admin_comment' => $request->validated('admin_comment'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.room-changes.index')
            ->with('success', 'Room change request rejected.');
    }
}
