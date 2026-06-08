<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewBookingRequestMail;
use App\Models\User;
use App\Services\BookingRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingRequestWebController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->is_invisible) {
            $user->update(['is_invisible' => false]);
        }

        $bookingRequest = BookingRequestService::createFromWeb($request, $user->id);

        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            Mail::to($admin)->queue(new NewBookingRequestMail($bookingRequest));
        }

        return redirect()->route('reservations.index');
    }
}
