<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminEventController extends Controller
{
    public function index(): Response
    {
        $events = Event::with('author:id,name')->orderBy('starts_at')->paginate(15);

        return Inertia::render('admin/events/Index', [
            'events' => $events,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/events/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        EventService::create($request, auth()->id());

        return redirect()->route('admin.events.index');
    }

    public function edit(int $id): Response
    {
        $event = Event::findOrFail($id);

        return Inertia::render('admin/events/Edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        EventService::update($request, $event);

        return redirect()->route('admin.events.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        EventService::softDelete($event);

        return redirect()->route('admin.events.index');
    }
}
