<?php

namespace App\Http\Controllers\Web;

use App\Models\Invite;
use App\Services\InviteService;
use Illuminate\Http\RedirectResponse;

class InviteWebController
{
    public function accept(int $id): RedirectResponse
    {
        $invite = Invite::where('id', $id)
            ->where('target_id', auth()->id())
            ->firstOrFail();

        InviteService::accept($invite);

        return redirect()->route('dashboard');
    }

    public function reject(int $id): RedirectResponse
    {
        $invite = Invite::where('id', $id)
            ->where('target_id', auth()->id())
            ->firstOrFail();

        InviteService::revoke($invite);

        return redirect()->route('dashboard');
    }
}
