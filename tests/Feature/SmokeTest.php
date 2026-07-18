<?php

namespace Tests\Feature;

use App\Enums\AllocationStatus;
use App\Enums\SeatStatus;
use App\Models\Meal;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\SeatApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function scaffold(): array
    {
        $admin = User::factory()->admin()->create();

        $student = Student::factory()->create();
        $student->user->update(['is_first_login' => false]);

        $room = Room::factory()->create(['room_no' => '101', 'floor' => 1]);
        $seat = Seat::factory()->for($room)->create(['status' => SeatStatus::Active]);
        SeatAllocation::factory()->create([
            'student_id' => $student->id,
            'seat_id' => $seat->id,
            'status' => AllocationStatus::Active,
        ]);

        $application = SeatApplication::factory()->create(['student_id' => $student->id]);
        $roomChange = RoomChangeRequest::factory()->create([
            'student_id' => $student->id,
            'current_seat_id' => $seat->id,
            'requested_room_id' => $room->id,
        ]);
        Meal::create(['student_id' => $student->id, 'date' => today()]);

        return compact('admin', 'student', 'room', 'seat', 'application', 'roomChange');
    }

    public function test_admin_pages_do_not_error(): void
    {
        $d = $this->scaffold();

        $routes = [
            ['admin.dashboard'], ['admin.change-password'], ['admin.students.index'],
            ['admin.rooms.index'], ['admin.rooms.create'], ['admin.rooms.show', $d['room']], ['admin.rooms.edit', $d['room']],
            ['admin.seats.index'], ['admin.seats.available'], ['admin.seats.occupied'], ['admin.seats.statistics'],
            ['admin.seats.allocate-form'], ['admin.seats.transfer-form', $d['seat']], ['admin.seats.vacate-form', $d['seat']],
            ['admin.applications.index'], ['admin.applications.show', $d['application']],
            ['admin.room-changes.index'], ['admin.room-changes.show', $d['roomChange']],
            ['admin.dining.index'], ['admin.dining.attendance'], ['admin.dining.daily-count'], ['admin.dining.monthly-report'],
            ['admin.reports.index'], ['admin.reports.students'], ['admin.reports.room-occupancy'],
            ['admin.reports.dining'], ['admin.reports.overview'],
            ['admin.settings.index'],
        ];

        $failures = [];
        foreach ($routes as $r) {
            $name = $r[0];
            $param = $r[1] ?? [];
            $response = $this->actingAs($d['admin'])->get(route($name, $param));
            if ($response->getStatusCode() >= 500) {
                $failures[] = $name.' :: '.($response->exception?->getMessage() ?? 'unknown');
            }
        }
        $this->assertSame([], $failures, "\n".implode("\n", $failures));
    }

    public function test_student_pages_do_not_error(): void
    {
        $d = $this->scaffold();
        $user = $d['student']->user;

        $routes = [
            'student.dashboard', 'student.profile', 'student.profile.edit', 'student.seat',
            'student.applications.index', 'student.applications.create',
            'student.room-changes.index', 'student.room-changes.create', 'student.dining.status',
        ];

        $failures = [];
        foreach ($routes as $name) {
            $response = $this->actingAs($user)->get(route($name));
            if ($response->getStatusCode() >= 500) {
                $failures[] = $name.' :: '.($response->exception?->getMessage() ?? 'unknown');
            }
        }
        $this->assertSame([], $failures, "\n".implode("\n", $failures));
    }
}
