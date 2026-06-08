<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewEventRegistrationMail;
use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EventRegistrationController extends Controller
{
    public function store(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        abort_if($event->starts_at->isPast(), 422, 'Регистрация на прошедшие события недоступна.');

        /** @var User $user */
        $user = auth()->user();

        DB::transaction(function () use ($event, $user): void {
            EventService::register($event, $user);

            $admins = User::where('is_admin', true)->get();
            foreach ($admins as $admin) {
                Mail::to($admin)->queue(new NewEventRegistrationMail($event, $user));
            }
        });

        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        abort_if($event->starts_at->isPast(), 422, 'Изменение записи на прошедшие события недоступно.');

        /** @var User $user */
        $user = auth()->user();

        EventService::unregister($event, $user);

        return back();
    }

    public function markSeen(): RedirectResponse
    {
        EventService::markAllRegistrationsSeen();

        return redirect()->route('admin.events.index');
    }
}
