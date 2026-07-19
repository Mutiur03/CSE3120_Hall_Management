<?php

namespace Database\Seeders;

use App\Enums\AllocationStatus;
use App\Enums\RoomChangeRequestStatus;
use App\Enums\RoomStatus;
use App\Enums\SeatApplicationStatus;
use App\Enums\SeatStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\DiningAttendance;
use App\Models\Meal;
use App\Models\Room;
use App\Models\RoomChangeRequest;
use App\Models\Seat;
use App\Models\SeatAllocation;
use App\Models\SeatApplication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Demo data for the Hall Management System.
 *
 * Creates an admin, 122 students (roll 2207000..2207121), rooms + seats,
 * seat allocations, seat applications, room change requests, meals, and
 * dining attendance so every module has realistic data to show.
 *
 * Idempotent: uses updateOrCreate keyed on natural keys, so it can be
 * re-run without duplicating rows. Does NOT wipe the database.
 *
 * Student email format: {lastname}{roll}@stud.kuet.ac.bd
 * Student default password: their roll number (must change on first login).
 */
class DemoSeeder extends Seeder
{
    /** First name pool (index-driven for deterministic output). */
    private const FIRST_NAMES = [
        'Abdullah', 'Rakib', 'Shahriar', 'Tanvir', 'Mahmudul', 'Fahim', 'Sabbir',
        'Imran', 'Jubayer', 'Ridwan', 'Arif', 'Nayeem', 'Naimur', 'Farhan',
        'Rifat', 'Zahid', 'Tanjil', 'Shakil', 'Mahin', 'Rezaul', 'Ashraful',
        'Mizanur', 'Sourav', 'Riyad', 'Nafis', 'Sifat', 'Mehedi', 'Tofael',
        'Rasel', 'Shafiul', 'Tahmid', 'Asif', 'Sajid', 'Rafsan', 'Emon',
    ];

    /** Last name pool used for both display name and email local-part. */
    private const LAST_NAMES = [
        'Islam', 'Rahman', 'Ahmed', 'Hossain', 'Chowdhury', 'Khan', 'Mia',
        'Uddin', 'Ali', 'Hasan', 'Kabir', 'Sarkar', 'Das', 'Roy', 'Sheikh',
        'Molla', 'Bhuiyan', 'Mahmud', 'Karim', 'Alam',
    ];

    /**
     * KUET department series codes. Roll = 22 (batch) + code + 3-digit serial.
     * e.g. CSE 07 -> 2207000, CE 01 -> 2201000.
     * Counts sum to 122 students.
     *
     * @var list<array{code: string, name: string, count: int}>
     */
    private const DEPARTMENTS = [
        ['code' => '01', 'name' => 'CE', 'count' => 21],
        ['code' => '03', 'name' => 'EEE', 'count' => 21],
        ['code' => '05', 'name' => 'ME', 'count' => 20],
        ['code' => '07', 'name' => 'CSE', 'count' => 20],
        ['code' => '08', 'name' => 'ECE', 'count' => 20],
        ['code' => '13', 'name' => 'IPE', 'count' => 20],
    ];

    public function run(): void
    {
        $today = Carbon::parse('2026-07-19');

        DB::transaction(function () use ($today) {
            $admin = $this->seedAdmin();
            $students = $this->seedStudents();
            $seats = $this->seedRoomsAndSeats();

            $allocations = $this->seedAllocations($admin, $students, $seats);
            $this->seedSeatApplications($students, $allocations);
            $this->seedRoomChangeRequests($admin, $students, $allocations, $seats);
            $this->seedMeals($students, $allocations, $today);
            $this->seedDiningAttendances($students, $allocations, $today);
        });
    }

    private function seedAdmin(): User
    {
        return User::query()->updateOrCreate(
            ['email' => 'admin@hall.edu'],
            [
                'name' => 'Hall Administrator',
                'password' => Hash::make('admin123'),
                'role' => UserRole::Admin,
                'is_first_login' => false,
                'is_active' => true,
            ]
        );
    }

    /**
     * @return array<int, Student> indexed by serial 0..121
     */
    private function seedStudents(): array
    {
        $students = [];
        $flat = 0; // running index across all departments

        // Total students (for marking the last few inactive).
        $total = array_sum(array_column(self::DEPARTMENTS, 'count'));

        foreach (self::DEPARTMENTS as $dept) {
            for ($serial = 0; $serial < $dept['count']; $serial++) {
                // roll = batch(22) + dept code + 3-digit serial
                $roll = '22'.$dept['code'].str_pad((string) $serial, 3, '0', STR_PAD_LEFT);

                $first = self::FIRST_NAMES[$flat % count(self::FIRST_NAMES)];
                $last = self::LAST_NAMES[$flat % count(self::LAST_NAMES)];
                $email = strtolower($last).$roll.'@stud.kuet.ac.bd';

                // Last 5 students inactive to exercise deactivation states.
                $isActive = $flat < ($total - 5);

                $user = User::query()->updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $first.' '.$last,
                        'password' => Hash::make($roll), // default password = roll
                        'role' => UserRole::Student,
                        'is_first_login' => true,
                        'is_active' => $isActive,
                    ]
                );

                $students[$flat] = Student::query()->updateOrCreate(
                    ['roll' => $roll],
                    [
                        'user_id' => $user->id,
                        'registration_no' => 'KUET-2022-'.$roll,
                        'department' => $dept['name'],
                        'academic_session' => '2022-2023',
                        'phone' => '017'.str_pad((string) (10000000 + $flat), 8, '0', STR_PAD_LEFT),
                        'status' => $isActive ? StudentStatus::Active : StudentStatus::Inactive,
                    ]
                );

                $flat++;
            }
        }

        return $students;
    }

    /**
     * 40 rooms across 5 floors, 4 seats each = 160 seats.
     *
     * @return array<int, Seat> flat list of seats in creation order
     */
    private function seedRoomsAndSeats(): array
    {
        $seats = [];
        $roomsPerFloor = 8;
        $capacity = 4;

        for ($floor = 1; $floor <= 5; $floor++) {
            for ($n = 1; $n <= $roomsPerFloor; $n++) {
                $roomNo = (string) ($floor * 100 + $n); // 101..108, 201..208, ...

                $room = Room::query()->updateOrCreate(
                    ['room_no' => $roomNo],
                    [
                        'floor' => $floor,
                        'capacity' => $capacity,
                        'status' => RoomStatus::Active,
                    ]
                );

                for ($s = 1; $s <= $capacity; $s++) {
                    $seatNo = 'S'.str_pad((string) $s, 2, '0', STR_PAD_LEFT);

                    $seats[] = Seat::query()->updateOrCreate(
                        ['room_id' => $room->id, 'seat_no' => $seatNo],
                        ['status' => SeatStatus::Active]
                    );
                }
            }
        }

        return $seats;
    }

    /**
     * Allocate active students to seats until seats run out.
     * Leaves later students unallocated so applications have realistic targets.
     *
     * @param  array<int, Student>  $students
     * @param  array<int, Seat>  $seats
     * @return array<int, SeatAllocation> keyed by student serial
     */
    private function seedAllocations(User $admin, array $students, array $seats): array
    {
        $allocations = [];
        $seatIndex = 0;

        foreach ($students as $serial => $student) {
            if ($seatIndex >= count($seats)) {
                break;
            }

            // Only allocate active students; cap at ~90 to leave applicants.
            if ($student->status !== StudentStatus::Active || $seatIndex >= 90) {
                continue;
            }

            $seat = $seats[$seatIndex];

            $exists = SeatAllocation::query()
                ->where('seat_id', $seat->id)
                ->where('status', AllocationStatus::Active)
                ->exists();

            if ($exists) {
                $seatIndex++;

                continue;
            }

            $allocations[$serial] = SeatAllocation::query()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'seat_id' => $seat->id,
                    'status' => AllocationStatus::Active,
                ],
                [
                    'allocated_by' => $admin->id,
                    'allocated_at' => '2026-06-01',
                    'vacated_at' => null,
                ]
            );

            $seatIndex++;
        }

        return $allocations;
    }

    /**
     * Seat applications for students without an active allocation.
     *
     * @param  array<int, Student>  $students
     * @param  array<int, SeatAllocation>  $allocations
     */
    private function seedSeatApplications(array $students, array $allocations): void
    {
        $rooms = Room::query()->orderBy('id')->get();
        $statuses = [
            SeatApplicationStatus::Pending,
            SeatApplicationStatus::Approved,
            SeatApplicationStatus::Rejected,
        ];
        $i = 0;

        foreach ($students as $serial => $student) {
            if (isset($allocations[$serial])) {
                continue; // already has a seat
            }

            if ($student->status !== StudentStatus::Active) {
                continue;
            }

            $status = $statuses[$i % count($statuses)];
            $room = $rooms[$i % $rooms->count()];

            SeatApplication::query()->updateOrCreate(
                ['student_id' => $student->id],
                [
                    'preferred_floor' => $room->floor,
                    'preferred_room_id' => $room->id,
                    'reason' => 'Requesting a hall seat for the 2022-2023 session.',
                    'status' => $status,
                    'admin_comment' => $status === SeatApplicationStatus::Pending
                        ? null
                        : 'Reviewed by hall office.',
                ]
            );

            $i++;
        }
    }

    /**
     * Pending room change requests for a handful of allocated students.
     *
     * @param  array<int, Student>  $students
     * @param  array<int, SeatAllocation>  $allocations
     * @param  array<int, Seat>  $seats
     */
    private function seedRoomChangeRequests(User $admin, array $students, array $allocations, array $seats): void
    {
        $rooms = Room::query()->orderBy('id', 'desc')->get();
        $picked = array_slice(array_keys($allocations), 0, 6, true);
        $i = 0;

        foreach ($picked as $serial) {
            $student = $students[$serial];
            $allocation = $allocations[$serial];
            $requestedRoom = $rooms[$i % $rooms->count()];

            RoomChangeRequest::query()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'requested_room_id' => $requestedRoom->id,
                ],
                [
                    'current_seat_id' => $allocation->seat_id,
                    'reason' => 'Prefer to move closer to department friends.',
                    'status' => RoomChangeRequestStatus::Pending,
                ]
            );

            $i++;
        }
    }

    /**
     * Meal records for allocated students: yesterday and today.
     *
     * @param  array<int, Student>  $students
     * @param  array<int, SeatAllocation>  $allocations
     */
    private function seedMeals(array $students, array $allocations, Carbon $today): void
    {
        $dates = [$today->copy()->subDay(), $today->copy()];

        foreach ($allocations as $serial => $allocation) {
            $student = $students[$serial];

            foreach ($dates as $date) {
                // Every 5th student turns meals off to exercise meal_active=false.
                $mealActive = ($serial % 5) !== 0;

                Meal::query()->updateOrCreate(
                    ['student_id' => $student->id, 'date' => $date->toDateString()],
                    [
                        'breakfast' => $mealActive,
                        'lunch' => $mealActive,
                        'dinner' => $mealActive,
                        'meal_active' => $mealActive,
                        'notes' => $mealActive ? null : 'Meal off requested.',
                    ]
                );
            }
        }
    }

    /**
     * Today's dining attendance (lunch + dinner) for allocated students.
     *
     * @param  array<int, Student>  $students
     * @param  array<int, SeatAllocation>  $allocations
     */
    private function seedDiningAttendances(array $students, array $allocations, Carbon $today): void
    {
        foreach ($allocations as $serial => $allocation) {
            $student = $students[$serial];

            foreach (['lunch', 'dinner'] as $mealType) {
                $present = ($serial % 3) !== 0; // ~2/3 present

                DiningAttendance::query()->updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'date' => $today->toDateString(),
                        'meal_type' => $mealType,
                    ],
                    [
                        'present' => $present,
                        'time' => $present ? ($mealType === 'lunch' ? '13:15' : '20:30') : null,
                    ]
                );
            }
        }
    }
}
