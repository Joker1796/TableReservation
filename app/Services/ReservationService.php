<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationService
{
    private static function createOrUpdate(Request $request, Reservation $reservation, ?int $authorId = null): Reservation
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:12'],
            'comment' => ['nullable', 'string'],
            'table_id' => ['sometimes', 'nullable', 'exists:tables,id'],
            'user_ids' => ['sometimes', 'nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $reservation->date = $validated['date'];
        $reservation->hours = $validated['hours'] ?? null;
        $reservation->comment = $validated['comment'] ?? null;

        if (array_key_exists('table_id', $validated)) {
            $reservation->table_id = $validated['table_id'];
        }

        $reservation->save();

        $userIds = array_unique(array_merge(
            $authorId !== null ? [$authorId] : [],
            $validated['user_ids'] ?? [],
        ));

        if (! empty($userIds)) {
            $reservation->users()->attach($userIds);
        }

        return $reservation;
    }

    public static function create(Request $request): Response
    {
        $reservation = self::createOrUpdate($request, new Reservation);
        $reservation->load('table', 'users');

        return response($reservation, 200);
    }

    public static function update(Request $request, Reservation $reservation): Response
    {
        $reservation = self::createOrUpdate($request, $reservation);
        $reservation->load('table', 'users');

        return response($reservation, 200);
    }

    public static function softDelete(Reservation $reservation): Response
    {
        $reservation->delete();

        return response($reservation, 200);
    }

    public static function attachUser(Reservation $reservation, User $user): Response
    {
        $reservation->users()->attach($user);
        $reservation->load('users');

        return response($reservation, 200);
    }

    public static function detachUser(Reservation $reservation, User $user): Response
    {
        $reservation->users()->detach($user);
        $reservation->load('users');

        return response($reservation, 200);
    }

    public static function createFromReservationRequest(ReservationRequest $rr): Reservation
    {
        $reservation = new Reservation;
        $reservation->date = $rr->date;
        $reservation->hours = $rr->hours;
        $reservation->comment = $rr->comment;
        $reservation->table_id = $rr->table_id;
        $reservation->save();

        return $reservation;
    }

    public static function createFromWeb(Request $request, int $authorId): void
    {
        self::createOrUpdate($request, new Reservation, $authorId);
    }
}
