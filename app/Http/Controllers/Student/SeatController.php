<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SeatController extends Controller
{
    public function show(): View
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $student->load(['currentAllocation.seat.room', 'currentAllocation.allocatedBy']);

        return view('student.seat.show', [
            'student' => $student,
            'allocation' => $student->currentAllocation,
        ]);
    }
}
