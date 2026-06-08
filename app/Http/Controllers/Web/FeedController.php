<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function index(Request $request): Response|JsonResponse
    {
        $cursor = $request->query('cursor');
        $page = FeedService::paginate($cursor, $request->user());

        if ($request->expectsJson()) {
            return response()->json($page);
        }

        return Inertia::render('feed/Index', [
            'items' => $page['data'],
            'nextCursor' => $page['next_cursor'],
            'upcomingEvents' => EventService::upcoming(),
            'recentEvents' => EventService::recent(),
        ]);
    }
}
