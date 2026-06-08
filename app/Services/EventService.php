<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventService
{
    public static function create(Request $request, int $authorId): Event
    {
        $validated = self::validate($request);

        $event = new Event;
        $event->title = $validated['title'];
        $event->short_description = $validated['short_description'] ?? null;
        $event->description = $validated['description'] ?? null;
        $event->starts_at = $validated['starts_at'];
        $event->ends_at = $validated['ends_at'] ?? null;
        $event->author_id = $authorId;
        $event->save();

        return $event;
    }

    public static function update(Request $request, Event $event): void
    {
        $validated = self::validate($request);

        $event->title = $validated['title'];
        $event->short_description = $validated['short_description'] ?? null;
        $event->description = $validated['description'] ?? null;
        $event->starts_at = $validated['starts_at'];
        $event->ends_at = $validated['ends_at'] ?? null;
        $event->save();
    }

    public static function softDelete(Event $event): void
    {
        $event->delete();
    }

    public static function register(Event $event, User $user): void
    {
        if (! $event->participants()->where('user_id', $user->id)->exists()) {
            $event->participants()->attach($user->id);
        }
    }

    public static function unregister(Event $event, User $user): void
    {
        $event->participants()->detach($user->id);
    }

    public static function markAllRegistrationsSeen(): void
    {
        DB::table('event_user')->update(['seen_by_admin' => true]);
    }

    /** @return Collection<int, Event> */
    public static function upcoming(int $limit = 5): Collection
    {
        return Event::with(['author:id,name', 'participants:id,name,phone,contacts'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit($limit)
            ->get();
    }

    /** @return Collection<int, Event> */
    public static function recent(int $limit = 2): Collection
    {
        return Event::with(['author:id,name', 'participants:id,name,phone,contacts'])
            ->where('starts_at', '<', now())
            ->orderByDesc('starts_at')
            ->limit($limit)
            ->get();
    }

    private static function validate(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
        ]);
    }
}
