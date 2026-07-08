<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentPasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user?->isStudent() || ! $user->is_first_login) {
            return $next($request);
        }

        if ($request->routeIs('student.change-password')) {
            return $next($request);
        }

        return redirect()
            ->route('student.change-password')
            ->with('warning', 'Please change your default password before continuing.');
    }
}
