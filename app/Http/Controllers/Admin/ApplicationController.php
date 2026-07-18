<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SeatApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveSeatApplicationRequest;
use App\Http\Requests\Admin\RejectSeatApplicationRequest;
use App\Models\SeatApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $query = SeatApplication::with(['student.user', 'preferredRoom']);

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

        $applications = $query->latest()->paginate(20)->withQueryString();

        return view('admin.applications.index', compact('applications'));
    }

    public function show(SeatApplication $application): View
    {
        $application->load(['student.user', 'preferredRoom', 'reviewer']);

        return view('admin.applications.show', compact('application'));
    }

    public function approve(ApproveSeatApplicationRequest $request, SeatApplication $application): RedirectResponse
    {
        if (! $application->isPending()) {
            return redirect()->back()->with('error', 'Application is not pending.');
        }

        $application->update([
            'status' => SeatApplicationStatus::Approved,
            'admin_comment' => $request->validated('admin_comment'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Application approved.');
    }

    public function reject(RejectSeatApplicationRequest $request, SeatApplication $application): RedirectResponse
    {
        if (! $application->isPending()) {
            return redirect()->back()->with('error', 'Application is not pending.');
        }

        $application->update([
            'status' => SeatApplicationStatus::Rejected,
            'admin_comment' => $request->validated('admin_comment'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Application rejected.');
    }
}
