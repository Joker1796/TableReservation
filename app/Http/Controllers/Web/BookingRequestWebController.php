<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BookingRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingRequestWebController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        BookingRequestService::createFromWeb($request, auth()->id());

        return redirect()->route('dashboard');
    }
}
