<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EditorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->is_editor && ! $user?->is_admin) {
            abort(403, 'Доступ запрещён.');
        }

        return $next($request);
    }
}
