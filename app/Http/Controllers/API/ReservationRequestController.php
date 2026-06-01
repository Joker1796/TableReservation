<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use App\Services\ReservationRequestService;
use Illuminate\Http\Request;

class ReservationRequestController extends Controller
{
    public function create(Request $request)
    {
        return ReservationRequestService::create($request);
    }

    public function show(ReservationRequest $reservationRequest)
    {
        return ReservationRequestService::show($reservationRequest);
    }

    public function update(Request $request, ReservationRequest $reservationRequest)
    {
        return ReservationRequestService::update($request, $reservationRequest);
    }

    public function softDelete(ReservationRequest $reservationRequest)
    {
        return ReservationRequestService::softDelete($reservationRequest);
    }

    public function attachUser(ReservationRequest $reservationRequest, User $user)
    {
        return ReservationRequestService::attachUser($reservationRequest, $user);
    }

    public function detachUser(ReservationRequest $reservationRequest, User $user)
    {
        return ReservationRequestService::detachUser($reservationRequest, $user);
    }

    public function associateTable(ReservationRequest $reservationRequest, Table $table)
    {
        return ReservationRequestService::associateTable($reservationRequest, $table);
    }

    public function deleteTable(ReservationRequest $reservationRequest)
    {
        return ReservationRequestService::deleteTable($reservationRequest);
    }
}
