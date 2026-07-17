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
