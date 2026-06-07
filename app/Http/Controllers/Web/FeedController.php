<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\FeedService;
use Inertia\Inertia;
use Inertia\Response;

class FeedController extends Controller
{
    public function index(): Response
    {
        $page = FeedService::paginate(null);

        return Inertia::render('feed/Index', [
            'items' => $page['data'],
            'nextCursor' => $page['next_cursor'],
            'upcomingEvents' => EventService::upcoming(),
            'recentEvents' => EventService::recent(),
        ]);
    }
}
