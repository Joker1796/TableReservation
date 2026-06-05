<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Services\InviteService;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReservationController extends Controller
{
    public function index(): Response
    {
        $reservations = Reservation::with(['table', 'users'])->latest()->paginate(10);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('admin/reservations/Index', [
            'reservations' => $reservations,
            'users' => $users,
        ]);
    }

    public function sendInvite(Request $request, int $id): RedirectResponse
    {
        InviteService::createForReservation($request, Reservation::findOrFail($id), auth()->id());

        return redirect()->route('admin.reservations.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        ReservationService::softDelete(Reservation::findOrFail($id));

        return redirect()->route('admin.reservations.index');
    }
}
