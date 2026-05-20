<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationRequest;
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

    public function attachReservationRequest(User $user, ReservationRequest $reservationRequest)
    {
        return UserService::attachReservationRequest($user, $reservationRequest);
    }

    public function detachReservationRequest(User $user, ReservationRequest $reservationRequest)
    {
        return UserService::detachReservationRequest($user, $reservationRequest);
    }
}
