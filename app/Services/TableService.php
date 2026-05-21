<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TableService
{
    public static function create(Request $request): Response
    {
        $table = new Table;

        return self::update($request, $table);
    }

    public static function update(Request $request, Table $table): Response
    {
        $table->name = $request->name;
        $table->description = $request->description ?? null;
        $table->status = $request->status ?? 0;

        $table->save();

        return response($table, 200);
    }

    public static function softDelete(Table $table): Response
    {
        $table->delete();

        return response($table, 200);
    }

    public static function addReservation(Table $table, Reservation $reservation): Response
    {
        $table->reservations()->save($reservation);
        $table->load('reservations');

        return response($table, 200);
    }

    public static function deleteReservation(Table $table, Reservation $reservation): Response
    {
        $table->reservations()->where('id', $reservation->id)->delete();
        $table->load('reservations');

        return response($table, 200);
    }

    public static function addReservationRequest(Table $table, ReservationRequest $reservationRequest): Response
    {
        $table->reservationRequests()->save($reservationRequest);
        $table->load('reservationRequests');

        return response($table, 200);
    }

    public static function deleteReservationRequest(Table $table, ReservationRequest $reservationRequest): Response
    {
        $table->reservationRequests()->where('id', $reservationRequest->id)->delete();
        $table->load('reservationRequests');

        return response($table, 200);
    }
}
