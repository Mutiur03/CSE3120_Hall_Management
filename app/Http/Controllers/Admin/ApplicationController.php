<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatApplication;
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
}
