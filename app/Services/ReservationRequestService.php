<?php

namespace App\Services;

use App\Enums\ReservationRequestStatus;
use App\Models\ReservationRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ReservationRequestService
{
    private static function createOrUpdate(Request $request, ReservationRequest $rr, ?int $authorId = null): ReservationRequest
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:12'],
            'comment' => ['nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'table_id' => ['sometimes', 'nullable', 'exists:tables,id'],
            'author' => ['sometimes', 'nullable', 'exists:users,id'],
            'user_ids' => ['sometimes', 'nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $rr->date = $validated['date'];
        $rr->hours = $validated['hours'] ?? null;
        $rr->comment = $validated['comment'] ?? null;
        $rr->status = $validated['status'] ?? $rr->status ?? 0;

        if (! $rr->author_id) {
            if ($authorId !== null) {
                $rr->author_id = $authorId;
            } elseif (! empty($validated['author'])) {
                $rr->author()->associate(User::find($validated['author']));
            } else {
                $rr->author()->associate($request->user());
            }
        }

        if (array_key_exists('table_id', $validated)) {
            $rr->table_id = $validated['table_id'];
        }

        $rr->save();

        if (array_key_exists('user_ids', $validated) || $authorId !== null) {
            $userIds = $validated['user_ids'] ?? [];
            if ($authorId !== null && ! in_array($authorId, $userIds)) {
                $userIds[] = $authorId;
            }
            $rr->users()->sync($userIds);
        }

        return $rr;
    }

    public static function create(Request $request): Response
    {
        $rr = self::createOrUpdate($request, new ReservationRequest);
        $rr->load('author', 'table', 'users');

        return response($rr, ResponseAlias::HTTP_OK);
    }

    public static function update(Request $request, ReservationRequest $rr): Response
    {
        $rr = self::createOrUpdate($request, $rr);
        $rr->load('author', 'table', 'users');

        return response($rr, ResponseAlias::HTTP_OK);
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

    public static function createFromWeb(Request $request, int $authorId): void
    {
        self::createOrUpdate($request, new ReservationRequest, $authorId);
    }

    public static function updateStatus(Request $request, ReservationRequest $rr, int $adminId): void
    {
        $validated = $request->validate([
            'status' => ['required', new Enum(ReservationRequestStatus::class)],
        ]);

        $newStatus = ReservationRequestStatus::from((int) $validated['status']);
        $rr->status = $newStatus;
        $rr->save();

        if ($newStatus === ReservationRequestStatus::APPROVED) {
            $reservation = ReservationService::createFromReservationRequest($rr);

            if ($rr->author_id) {
                $reservation->users()->attach($rr->author_id);
            }

            foreach ($rr->users as $user) {
                if ($user->id === $rr->author_id) {
                    continue;
                }

                InviteService::createPending($adminId, $user->id, $reservation->id);
            }
        }
    }

    public static function assignTable(Request $request, ReservationRequest $rr): void
    {
        $validated = $request->validate([
            'table_id' => ['nullable', 'exists:tables,id'],
        ]);

        $rr->table_id = $validated['table_id'];
        $rr->save();
    }
}
