<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($role === UserRole::Admin->value && ! $user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($role === UserRole::Student->value && $user->role !== UserRole::Student) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
