<?php

namespace App\Services;

use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
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
        $validated = $request->validate([
            'comment' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:12'],
            'status' => ['nullable', 'integer', 'min:0'],
            'table_id' => ['nullable', 'exists:tables,id'],
            'author' => ['nullable', 'exists:users,id'],
            'users' => ['nullable', 'array'],
            'users.*' => ['exists:users,id'],
        ]);

        $reservationRequest->comment = $validated['comment'] ?? null;
        $reservationRequest->date = $validated['date'];
        $reservationRequest->hours = $validated['hours'] ?? null;
        $reservationRequest->status = $validated['status'] ?? 0;

        if (! $reservationRequest->author) {
            if (! empty($validated['author'])) {
                $reservationRequest->author()->associate(User::find($validated['author']));
            } else {
                $reservationRequest->author()->associate($request->user());
            }
        }

        if (! empty($validated['table_id'])) {
            $reservationRequest->table()->associate(Table::find($validated['table_id']));
        }

        $reservationRequest->save();

        if (! empty($validated['users'])) {
            $reservationRequest->users()->attach(User::find($validated['users']));
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
