<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();

        $students = Student::query()
            ->with(['user', 'currentAllocation.seat.room'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('roll', 'like', "%{$search}%")
                        ->orWhere('registration_no', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('roll')
            ->paginate(20)
            ->withQueryString();

        return view('admin.students.index', compact('students', 'search'));
    }

    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('session', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->latest()->paginate(20)->withQueryString();

        $departments = Student::select('department')->distinct()->pluck('department');
        $sessions = Student::select('session')->distinct()->pluck('session');

        return view('admin.students.index', compact('students', 'departments', 'sessions'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|unique:students,student_id',
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'session' => 'required|string|max:255',
            'batch' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:students,email',
            'address' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        $validated['password'] = Hash::make($validated['student_id']);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student added successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['seatAllocations.seat.room', 'seatApplications', 'roomChangeRequests', 'meals']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'session' => 'required|string|max:255',
            'batch' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => ['required', 'email', Rule::unique('students')->ignore($student->id)],
            'address' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deactivated successfully.');
    }

    public function toggleStatus(Student $student)
    {
        $student->status = $student->status === 'active' ? 'inactive' : 'active';
        $student->save();

        return redirect()->back()
            ->with('success', "Student status updated to {$student->status}.");
    }
}
