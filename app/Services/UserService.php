<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\User;
use Illuminate\Http\Response;

class UserService
{
    public static function attachReservation(User $user, Reservation $reservation): Response
    {
        $user->reservations()->attach($reservation);
        $user->load('reservations');

        return response($user, 200);
    }

    public static function detachReservation(User $user, Reservation $reservation): Response
    {
        $user->reservations()->detach($reservation);
        $user->load('reservations');

        return response($user, 200);
    }

    public static function attachReservationRequest(User $user, ReservationRequest $reservationRequest): Response
    {
        $user->reservationRequests()->attach($reservationRequest);
        $user->load('reservationRequests');

        return response($user, 200);
    }

    public static function detachReservationRequest(User $user, ReservationRequest $reservationRequest): Response
    {
        $user->reservationRequests()->detach($reservationRequest);
        $user->load('reservationRequests');

        return response($user, 200);
    }

    public static function regenerateApiToken(User $user): string
    {
        $user->tokens()->delete();

        return $user->createToken('api-token')->plainTextToken;
    }
}
