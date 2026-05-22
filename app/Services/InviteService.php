<?php

namespace App\Services;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class InviteService
{
    public static function create(Request $request): Response
    {
        $validated = $request->validate([
            'status' => ['nullable', new Enum(InviteStatus::class)],
            'author_id' => ['required', 'exists:users,id'],
            'target_id' => ['required', 'exists:users,id'],
            'reservation_id' => ['required', 'exists:reservations,id'],
        ]);

        $invite = new Invite;

        $invite->status = $validated['status'] ?? InviteStatus::PENDING->value;
        $invite->author()->associate(User::find($validated['author_id']));
        $invite->target()->associate(User::find($validated['target_id']));
        $invite->reservation()->associate(User::find($validated['reservation_id']));

        $invite->save();

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
}
