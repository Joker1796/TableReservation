<?php

namespace App\Services;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class InviteService
{
    private static function make(int $authorId, int $targetId, int $reservationId, InviteStatus $status = InviteStatus::PENDING): Invite
    {
        $invite = new Invite;
        $invite->author_id = $authorId;
        $invite->target_id = $targetId;
        $invite->reservation_id = $reservationId;
        $invite->status = $status;
        $invite->save();

        return $invite;
    }

    public static function createPending(int $authorId, int $targetId, int $reservationId): void
    {
        self::make($authorId, $targetId, $reservationId);
    }

    public static function create(Request $request): Response
    {
        $validated = $request->validate([
            'status' => ['nullable', new Enum(InviteStatus::class)],
            'author_id' => ['required', 'exists:users,id'],
            'target_id' => ['required', 'exists:users,id'],
            'reservation_id' => ['required', 'exists:reservations,id'],
        ]);

        $status = ! empty($validated['status']) ? InviteStatus::from($validated['status']) : InviteStatus::PENDING;
        $invite = self::make($validated['author_id'], $validated['target_id'], $validated['reservation_id'], $status);

        return response($invite, ResponseAlias::HTTP_OK);
    }

    public static function show(Invite $invite): Response
    {
        return response($invite, ResponseAlias::HTTP_OK);
    }

    public static function setStatus(Invite $invite, InviteStatus $status): Response
    {
        $invite->status = $status;
        $invite->save();

        return response($invite, ResponseAlias::HTTP_OK);
    }

    public static function accept(Invite $invite): Response
    {
        return response($invite->accept(), ResponseAlias::HTTP_OK);
    }

    public static function revoke(Invite $invite): Response
    {
        return response($invite->revoke(), ResponseAlias::HTTP_OK);
    }

    public static function softDelete(Invite $invite): Response
    {
        $invite->delete();

        return response($invite, ResponseAlias::HTTP_OK);
    }

    public static function createForReservation(Request $request, Reservation $reservation, int $authorId): void
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        self::make($authorId, $validated['user_id'], $reservation->id);
    }
}
