<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasContacts
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasContacts()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Заполните контактные данные в профиле.']);

            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}
