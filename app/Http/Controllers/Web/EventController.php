<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('events/Index', [
            'upcomingEvents' => EventService::upcoming(20),
            'recentEvents' => EventService::recent(10),
        ]);
    }
}
