<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewBookingRequestMail;
use App\Models\User;
use App\Services\BookingRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class BookingRequestWebController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->is_admin && ! $user->is_editor
            && BookingRequestService::dailyCountForUser($user->id) >= 3
        ) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Лимит заявок на сегодня исчерпан (максимум 3).']);

            return redirect()->route('reservations.index');
        }

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
