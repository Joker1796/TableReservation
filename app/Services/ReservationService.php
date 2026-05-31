<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationService
{
    public static function create(Request $request): Response
    {
        $reservation = new Reservation;

        return self::update($request, $reservation);
    }

    public static function update(Request $request, Reservation $reservation): Response
    {
        $validated = $request->validate([
            'comment' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:12'],
            'table_id' => ['sometimes', 'nullable', 'exists:tables,id'],
            'users' => ['sometimes', 'nullable', 'array'],
            'users.*' => ['exists:users,id'],
        ]);

        $reservation->comment = $validated['comment'];
        $reservation->date = $validated['date'];
        $reservation->hours = $validated['hours'];

        if (! empty($validated['table_id'])) {
            $table = Table::find($validated['table_id']);
            $reservation->table()->associate($table);
        }

        $reservation->save();

        if (! empty($validated['users'])) {
            $users = User::find($validated['users']);
            $reservation->users()->attach($users);
        }

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
}
