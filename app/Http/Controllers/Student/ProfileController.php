<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateStudentContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $student = $user->student;

        abort_if($student === null, 404);

        $student->load(['currentAllocation.seat.room']);

        return view('student.profile.show', compact('user', 'student'));
    }

    public function edit(): View
    {
        $user = auth()->user();
        $student = $user->student;

        abort_if($student === null, 404);

        return view('student.profile.edit', compact('user', 'student'));
    }

    public function update(UpdateStudentContactRequest $request): RedirectResponse
    {
        $student = auth()->user()->student;

        abort_if($student === null, 404);

        $student->update($request->validated());

        return redirect()
            ->route('student.profile')
            ->with('success', 'Contact information updated successfully.');
    }
}
