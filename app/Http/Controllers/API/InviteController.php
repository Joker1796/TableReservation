<?php

namespace App\Http\Controllers\API;

use App\Enums\InviteStatus;
use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Services\InviteService;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function create(Request $request)
    {
        return InviteService::create($request);
    }

    public function show(Invite $invite)
    {
        return InviteService::show($invite);
    }

    public function setStatus(Invite $invite, InviteStatus $status)
    {
        return InviteService::setStatus($invite, $status);
    }

    public function accept(Invite $invite)
    {
        return InviteService::accept($invite);
    }

    public function revoke(Invite $invite)
    {
        return InviteService::revoke($invite);
    }

    public function softDelete(Invite $invite)
    {
        return InviteService::softDelete($invite);
    }
}
