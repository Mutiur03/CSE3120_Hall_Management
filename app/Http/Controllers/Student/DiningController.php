<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;

class DiningController extends Controller
{
    public function status()
    {
        $student = auth('student')->user();
        $meals = $student->meals()->latest()->limit(30)->get();
        $todayMeal = Meal::where('student_id', $student->id)->where('date', today())->first();

        return view('student.dining.status', compact('meals', 'todayMeal'));
    }

    public function toggleMeal(Request $request)
    {
        $student = auth('student')->user();
        $meal = Meal::firstOrCreate(
            ['student_id' => $student->id, 'date' => today()],
            ['breakfast' => true, 'lunch' => true, 'dinner' => true, 'meal_active' => true]
        );

        $meal->update(['meal_active' => !$meal->meal_active]);

        $status = $meal->meal_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Meal has been {$status}.");
    }

    public function updateMealPreference(Request $request)
    {
        $student = auth('student')->user();
        $meal = Meal::firstOrCreate(
            ['student_id' => $student->id, 'date' => today()],
            ['breakfast' => true, 'lunch' => true, 'dinner' => true, 'meal_active' => true]
        );

        $meal->update([
            'breakfast' => $request->has('breakfast'),
            'lunch' => $request->has('lunch'),
            'dinner' => $request->has('dinner'),
        ]);

        return redirect()->back()->with('success', 'Meal preferences updated.');
    }
}
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
