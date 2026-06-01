<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\Table;
use App\Services\TableService;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function create(Request $request)
    {
        return TableService::create($request);
    }

    public function show(Table $table)
    {
        return response($table, 200);
    }

    public function update(Request $request, Table $table)
    {
        return TableService::update($request, $table);
    }

    public function softDelete(Table $table)
    {
        return TableService::softDelete($table);
    }

    public function addReservation(Table $table, Reservation $reservation)
    {
        return TableService::addReservation($table, $reservation);
    }

    public function deleteReservation(Table $table, Reservation $reservation)
    {
        return TableService::deleteReservation($table, $reservation);
    }

    public function addReservationRequest(Table $table, ReservationRequest $rr)
    {
        return TableService::addReservationRequest($table, $rr);
    }

    public function deleteReservationRequest(Table $table, ReservationRequest $rr)
    {
        return TableService::deleteReservationRequest($table, $rr);
    }
}
