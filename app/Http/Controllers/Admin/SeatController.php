<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AllocationStatus;
use App\Enums\SeatStatus;
use App\Enums\StudentStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SeatController extends Controller
{
    public function index(Request $request): View
    {
        $query = Seat::query()->with(['room', 'currentAllocation.student.user']);

        if ($request->filled('floor')) {
            $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('floor', $request->integer('floor')));
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->integer('room_id'));
        }

        if ($request->filled('status')) {
            match ($request->string('status')->value()) {
                'available' => $query->available(),
                'occupied' => $query->occupied(),
                'inactive' => $query->where('status', SeatStatus::Inactive),
                default => null,
            };
        }

        $seats = $query->orderBy('room_id')->orderBy('seat_no')->paginate(50)->withQueryString();
        $floors = Room::query()->select('floor')->distinct()->orderBy('floor')->pluck('floor');
        $rooms = Room::query()->orderBy('room_no')->get(['id', 'room_no']);

        return view('admin.seats.index', compact('seats', 'floors', 'rooms'));
    }

    public function available(Request $request): View
    {
        $query = Seat::query()
            ->with('room')
            ->available();

        if ($request->filled('floor')) {
            $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('floor', $request->integer('floor')));
        }

        $seats = $query->orderBy('room_id')->orderBy('seat_no')->paginate(50)->withQueryString();
        $floors = Room::query()->select('floor')->distinct()->orderBy('floor')->pluck('floor');
        $unallocatedStudents = $this->unallocatedStudents();

        return view('admin.seats.available', compact('seats', 'floors', 'unallocatedStudents'));
    }

    public function occupied(Request $request): View
    {
        $query = Seat::query()
            ->with(['room', 'currentAllocation.student.user'])
            ->occupied();

        if ($request->filled('floor')) {
            $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('floor', $request->integer('floor')));
        }

        $seats = $query->orderBy('room_id')->orderBy('seat_no')->paginate(50)->withQueryString();
        $floors = Room::query()->select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('admin.seats.occupied', compact('seats', 'floors'));
    }

    public function statistics(): View
    {
        $totalSeats = Seat::count();
        $occupiedSeats = Seat::occupied()->count();
        $availableSeats = Seat::available()->count();
        $inactiveSeats = Seat::where('status', SeatStatus::Inactive)->count();
        $occupancyPercentage = $totalSeats > 0 ? round(($occupiedSeats / $totalSeats) * 100, 2) : 0;

        $floorStats = Room::query()
            ->select('floor')
            ->distinct()
            ->orderBy('floor')
            ->pluck('floor')
            ->map(function (int $floor) {
                $totalSeatsOnFloor = Seat::query()
                    ->whereHas('room', fn ($roomQuery) => $roomQuery->where('floor', $floor))
                    ->count();
                $occupiedSeatsOnFloor = Seat::query()
                    ->whereHas('room', fn ($roomQuery) => $roomQuery->where('floor', $floor))
                    ->occupied()
                    ->count();

                return (object) [
                    'floor' => $floor,
                    'total_seats' => $totalSeatsOnFloor,
                    'occupied_seats' => $occupiedSeatsOnFloor,
                    'available_seats' => $totalSeatsOnFloor - $occupiedSeatsOnFloor,
                    'occupancy_rate' => $totalSeatsOnFloor > 0
                        ? round(($occupiedSeatsOnFloor / $totalSeatsOnFloor) * 100, 2)
                        : 0,
                ];
            });

        return view('admin.seats.statistics', compact(
            'totalSeats',
            'occupiedSeats',
            'availableSeats',
            'inactiveSeats',
            'occupancyPercentage',
            'floorStats',
        ));
    }

    public function allocateForm(): View
    {
        return view('admin.seats.allocate', [
            'seats' => Seat::query()->with('room')->available()->orderBy('room_id')->orderBy('seat_no')->get(),
            'students' => $this->unallocatedStudents(),
        ]);
    }

    public function allocate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seat_id' => ['required', 'integer', 'exists:seats,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                $student = Student::query()->lockForUpdate()->findOrFail($validated['student_id']);
                $seat = Seat::query()->lockForUpdate()->with('room')->findOrFail($validated['seat_id']);

                $this->assertStudentCanReceiveSeat($student);
                $this->assertSeatCanBeAllocated($seat);

                SeatAllocation::create([
                    'student_id' => $student->id,
                    'seat_id' => $seat->id,
                    'allocated_by' => auth()->id(),
                    'allocated_at' => now()->toDateString(),
                    'status' => AllocationStatus::Active,
                ]);
            });
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()
            ->route('admin.seats.occupied')
            ->with('success', 'Seat allocated successfully.');
    }

    public function vacateForm(Seat $seat): View|RedirectResponse
    {
        $seat->load(['room', 'currentAllocation.student.user']);

        if (! $seat->currentAllocation) {
            return redirect()
                ->route('admin.seats.index')
                ->with('error', 'This seat is not currently occupied.');
        }

        return view('admin.seats.vacate', compact('seat'));
    }

    public function vacate(Seat $seat): RedirectResponse
    {
        try {
            DB::transaction(function () use ($seat): void {
                $lockedSeat = Seat::query()->lockForUpdate()->findOrFail($seat->id);
                $allocation = SeatAllocation::query()
                    ->where('seat_id', $lockedSeat->id)
                    ->where('status', AllocationStatus::Active)
                    ->lockForUpdate()
                    ->first();

                if (! $allocation) {
                    throw ValidationException::withMessages([
                        'seat' => 'This seat is not currently occupied.',
                    ]);
                }

                $allocation->update([
                    'status' => AllocationStatus::Vacated,
                    'vacated_at' => now()->toDateString(),
                ]);
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()
            ->route('admin.seats.available')
            ->with('success', 'Seat vacated successfully.');
    }

    public function transferForm(Seat $seat): View|RedirectResponse
    {
        $seat->load(['room', 'currentAllocation.student.user']);

        if (! $seat->currentAllocation) {
            return redirect()
                ->route('admin.seats.index')
                ->with('error', 'This seat is not currently occupied.');
        }

        return view('admin.seats.transfer', [
            'seat' => $seat,
            'targetSeats' => Seat::query()
                ->with('room')
                ->whereKeyNot($seat->id)
                ->available()
                ->orderBy('room_id')
                ->orderBy('seat_no')
                ->get(),
        ]);
    }

    public function transfer(Request $request, Seat $seat): RedirectResponse
    {
        $validated = $request->validate([
            'target_seat_id' => ['required', 'integer', 'exists:seats,id', 'not_in:'.$seat->id],
        ]);

        try {
            DB::transaction(function () use ($seat, $validated): void {
                $currentSeat = Seat::query()->lockForUpdate()->findOrFail($seat->id);
                $targetSeat = Seat::query()->lockForUpdate()->with('room')->findOrFail($validated['target_seat_id']);

                $allocation = SeatAllocation::query()
                    ->where('seat_id', $currentSeat->id)
                    ->where('status', AllocationStatus::Active)
                    ->lockForUpdate()
                    ->first();

                if (! $allocation) {
                    throw ValidationException::withMessages([
                        'seat' => 'This seat is not currently occupied.',
                    ]);
                }

                $this->assertSeatCanBeAllocated($targetSeat);

                $allocation->update([
                    'status' => AllocationStatus::Vacated,
                    'vacated_at' => now()->toDateString(),
                ]);

                SeatAllocation::create([
                    'student_id' => $allocation->student_id,
                    'seat_id' => $targetSeat->id,
                    'allocated_by' => auth()->id(),
                    'allocated_at' => now()->toDateString(),
                    'status' => AllocationStatus::Active,
                ]);
            });
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()
            ->route('admin.seats.occupied')
            ->with('success', 'Seat transferred successfully.');
    }

    /**
     * @return Collection<int, Student>
     */
    private function unallocatedStudents()
    {
        return Student::query()
            ->with('user')
            ->where('status', StudentStatus::Active)
            ->whereDoesntHave('currentAllocation')
            ->orderBy('roll')
            ->get();
    }

    private function assertStudentCanReceiveSeat(Student $student): void
    {
        if ($student->status !== StudentStatus::Active) {
            throw ValidationException::withMessages([
                'student_id' => 'Only active students can receive a seat.',
            ]);
        }

        if ($student->currentAllocation()->exists()) {
            throw ValidationException::withMessages([
                'student_id' => 'This student already has an active seat allocation.',
            ]);
        }
    }

    private function assertSeatCanBeAllocated(Seat $seat): void
    {
        if ($seat->status !== SeatStatus::Active) {
            throw ValidationException::withMessages([
                'seat_id' => 'This seat is not available for allocation.',
            ]);
        }

        if ($seat->currentAllocation()->exists()) {
            throw ValidationException::withMessages([
                'seat_id' => 'This seat is already occupied.',
            ]);
        }
    }
}
