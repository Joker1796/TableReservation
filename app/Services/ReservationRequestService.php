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
                $reservationRequest->user()->associate($author);
            } elseif ($request->user()) {
                $reservationRequest->user()->associate($request->user());
            } else {
                //TODO костыль исправить добавив авторизацию
                $author = User::find(1);
                $reservationRequest->user()->associate($author);
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

        $reservationRequest = ReservationRequest::find($reservationRequest->id);

        return response($reservationRequest, ResponseAlias::HTTP_OK);
    }

    public static function show(ReservationRequest $reservationRequest): Response
    {
        return response($reservationRequest, 200);
    }

    public static function softDelete(ReservationRequest $reservationRequest): Response
    {
        $reservationRequest->delete();

        return response($reservationRequest, 200);
    }

    public static function attachUser(ReservationRequest $reservationRequest, User $user): Response
    {
        $reservationRequest->users()->attach($user);

        return response($reservationRequest, 200);
    }

    public static function detachUser(ReservationRequest $reservationRequest, User $user): Response
    {
        $reservationRequest->users()->detach($user);

        return response($reservationRequest, 200);
    }
}
