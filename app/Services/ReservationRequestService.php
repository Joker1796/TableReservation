<?php

namespace App\Services;

use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ReservationRequestService
{
    public static function create(Request $request): Response
    {
        $reservationRequest = new ReservationRequest;

        return self::update($request, $reservationRequest);
    }

    public static function update(Request $request, ReservationRequest $reservationRequest): Response
    {
        $reservationRequest->comment = $request->comment ?? null;
        $reservationRequest->date = Carbon::parse($request->date);
        $reservationRequest->hours = $request->hours ?? null;
        $reservationRequest->status = $request->status ?? 0;

        if (! $reservationRequest->author) {
            if ($request->author) {
                $author = User::find($request->author);
                $reservationRequest->author()->associate($author);
            } else {
                $reservationRequest->author()->associate($request->user());
            }
        }

        if ($request->table_id) {
            $table = Table::find($request->table_id);
            $reservationRequest->table()->associate($table);
        }

        $reservationRequest->save();

        if ($request->users) {
            $users = User::find($request->users);
            $reservationRequest->users()->attach($users);
        }

        $reservationRequest->load('author', 'table', 'users');

        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }

    public static function show(ReservationRequest $reservationRequest): Response
    {
        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }

    public static function softDelete(ReservationRequest $reservationRequest): Response
    {
        $reservationRequest->delete();

        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }

    public static function attachUser(ReservationRequest $reservationRequest, User $user): Response
    {
        $reservationRequest->users()->attach($user);
        $reservationRequest->load('users');

        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }

    public static function detachUser(ReservationRequest $reservationRequest, User $user): Response
    {
        $reservationRequest->users()->detach($user);
        $reservationRequest->load('users');

        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }

    public static function associateTable(ReservationRequest $reservationRequest, Table $table): Response
    {
        $reservationRequest->table()->associate($table);
        $reservationRequest->save();

        $reservationRequest->load('table');

        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }

    public static function deleteTable(ReservationRequest $reservationRequest): Response
    {
        $reservationRequest->table()->dissociate();
        $reservationRequest->table()->delete();
        $reservationRequest->save();

        $reservationRequest->load('table');

        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }
}
