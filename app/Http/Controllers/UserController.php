<?php

namespace App\Http\Controllers;

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
}
