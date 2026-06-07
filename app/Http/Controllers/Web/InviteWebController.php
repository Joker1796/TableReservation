<?php

namespace App\Http\Controllers\Web;

use App\Enums\InviteStatus;
use App\Models\Invite;
use App\Services\InviteService;
use Illuminate\Http\RedirectResponse;

class InviteWebController
{
    public function accept(int $id): RedirectResponse
    {
        $invite = Invite::where('id', $id)
            ->where('target_id', auth()->id())
            ->where('status', InviteStatus::PENDING)
            ->firstOrFail();

        InviteService::accept($invite);

        return redirect()->route('reservations.index');
    }

    public function reject(int $id): RedirectResponse
    {
        $invite = Invite::where('id', $id)
            ->where('target_id', auth()->id())
            ->where('status', InviteStatus::PENDING)
            ->firstOrFail();

        InviteService::revoke($invite);

        return redirect()->route('reservations.index');
    }
}
