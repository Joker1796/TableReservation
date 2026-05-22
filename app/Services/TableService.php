<?php

namespace App\Services;

use App\Enums\TableStatus;
use App\Models\Reservation;
use App\Models\ReservationRequest;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;

class TableService
{
    public static function create(Request $request): Response
    {
        $table = new Table;

        return self::update($request, $table);
    }

    public static function update(Request $request, Table $table): Response
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', new Enum(TableStatus::class)],
        ]);

        $table->name = $validated['name'];
        $table->description = $validated['description'];
        $table->status = $validated['status'];

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
