<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
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
}
