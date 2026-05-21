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
        $reservation->comment = $request->comment ?? null;
        $reservation->date = $request->date;
        $reservation->hours = $request->hours ?? null;

        if ($request->table) {
            $table = Table::find($request->table);
            $reservation->table()->associate($table);
        }

        $reservation->save();

        if ($request->users) {
            $users = User::find($request->users);
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
