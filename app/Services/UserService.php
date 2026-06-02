<?php

namespace App\Services;

use App\Models\BookingRequest;
use App\Models\Reservation;
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

    public static function attachBookingRequest(User $user, BookingRequest $bookingRequest): Response
    {
        $user->bookingRequests()->attach($bookingRequest);
        $user->load('bookingRequests');

        return response($user, 200);
    }

    public static function detachBookingRequest(User $user, BookingRequest $bookingRequest): Response
    {
        $user->bookingRequests()->detach($bookingRequest);
        $user->load('bookingRequests');

        return response($user, 200);
    }

    public static function regenerateApiToken(User $user): string
    {
        $user->tokens()->delete();

        return $user->createToken('api-token')->plainTextToken;
    }
}
