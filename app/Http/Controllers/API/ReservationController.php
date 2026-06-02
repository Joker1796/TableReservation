<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function create(Request $request)
    {
        return ReservationService::create($request);
    }

    public function show(Reservation $reservation)
    {
        return response($reservation, 200);
    }

    public function update(Request $request, Reservation $reservation)
    {
        return ReservationService::update($request, $reservation);
    }

    public function softDelete(Reservation $reservation)
    {
        return ReservationService::softDelete($reservation);
    }

    public function attachUser(Reservation $reservation, User $user)
    {
        abort_unless($reservation->users()->where('user_id', auth()->id())->exists(), 403);

        return ReservationService::attachUser($reservation, $user);
    }

    public function detachUser(Reservation $reservation, User $user)
    {
        abort_unless($reservation->users()->where('user_id', auth()->id())->exists(), 403);

        return ReservationService::detachUser($reservation, $user);
    }
}
