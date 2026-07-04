<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomStatus;
use App\Enums\SeatStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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

    public function create(): View
    {
        $statuses = RoomStatus::cases();

        return view('admin.rooms.create', compact('statuses'));
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $room = DB::transaction(function () use ($data): Room {
            $room = Room::create($data);
            $this->syncSeats($room);

            return $room;
        });

        return redirect()->route('admin.rooms.show', $room)
            ->with('success', "Room {$room->room_no} created successfully.");
    }

    public function show(Room $room): View
    {
        $room->load([
            'seats' => fn ($query) => $query->orderBy('seat_no'),
            'seats.currentAllocation.student.user',
        ]);

        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $statuses = RoomStatus::cases();

        return view('admin.rooms.edit', compact('room', 'statuses'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $data = $request->validated();

        $occupied = $room->seats()->whereHas('currentAllocation')->count();

        if ($data['capacity'] < $occupied) {
            return back()
                ->withInput()
                ->withErrors(['capacity' => "Capacity cannot be lower than {$occupied} occupied seat(s)."]);
        }

        DB::transaction(function () use ($room, $data): void {
            $room->update($data);
            $this->syncSeats($room);
        });

        return redirect()->route('admin.rooms.show', $room)
            ->with('success', "Room {$room->room_no} updated successfully.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->seats()->whereHas('currentAllocation')->exists()) {
            return back()->withErrors([
                'room' => 'Cannot delete a room that still has occupied seats.',
            ]);
        }

        $room->seats()->delete();
        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    /**
     * Reconcile the room's seats with its capacity. Adds seats when capacity
     * grows; removes only vacant seats when it shrinks.
     */
    private function syncSeats(Room $room): void
    {
        $existing = $room->seats()->orderBy('id')->get();
        $current = $existing->count();

        if ($current < $room->capacity) {
            $taken = $existing->pluck('seat_no')->all();

            for ($i = 1; $current < $room->capacity; $i++) {
                $seatNo = "{$room->room_no}-{$i}";

                if (in_array($seatNo, $taken, true)) {
                    continue;
                }

                $room->seats()->create([
                    'seat_no' => $seatNo,
                    'status' => SeatStatus::Active,
                ]);

                $taken[] = $seatNo;
                $current++;
            }

            return;
        }

        if ($current > $room->capacity) {
            $removable = $existing
                ->filter(fn ($seat) => ! $seat->currentAllocation()->exists())
                ->sortByDesc('id')
                ->values();

            $toRemove = $current - $room->capacity;

            foreach ($removable->take($toRemove) as $seat) {
                $seat->delete();
            }
        }
    }
}
