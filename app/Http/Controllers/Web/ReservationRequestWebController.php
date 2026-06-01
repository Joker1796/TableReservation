<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ReservationRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReservationRequestWebController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        ReservationRequestService::createFromWeb($request, auth()->id());

        return redirect()->route('dashboard');
    }
}
