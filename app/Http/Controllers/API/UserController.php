<?php

namespace App\Http\Controllers\API;

use App\Models\BookingRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Services\UserService;

class UserController
{
    public function attachReservation(User $user, Reservation $reservation)
    {
        return UserService::attachReservation($user, $reservation);
    }

    public function detachReservation(User $user, Reservation $reservation)
    {
        return UserService::detachReservation($user, $reservation);
    }

    public function attachBookingRequest(User $user, BookingRequest $bookingRequest)
    {
        return UserService::attachBookingRequest($user, $bookingRequest);
    }

    public function detachBookingRequest(User $user, BookingRequest $bookingRequest)
    {
        return UserService::detachBookingRequest($user, $bookingRequest);
    }
}
