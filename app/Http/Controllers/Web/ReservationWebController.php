<?php

namespace App\Http\Controllers\Web;

use App\Enums\BookingRequestStatus;
use App\Enums\InviteStatus;
use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\Invite;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationWebController extends Controller
{
    public function index(Request $request): Response
    {
        $date = $request->date ?? now()->toDateString();
        $userId = auth()->id();

        $reservations = Reservation::with(['table', 'users'])
            ->whereDate('date', $date)
            ->latest()
            ->get();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        $myReservationDates = Reservation::whereHas('users', fn ($q) => $q->where('user_id', $userId))
            ->pluck('date')->map(fn ($d) => substr($d, 0, 10))->unique()->values();

        $myRequestDates = BookingRequest::where(function ($q) use ($userId) {
            $q->where('author_id', $userId)
                ->orWhereHas('users', fn ($q2) => $q2->where('user_id', $userId));
        })->where('status', BookingRequestStatus::PENDING)
            ->pluck('date')->map(fn ($d) => substr($d, 0, 10))->unique()->values();

        $myInviteDates = Invite::where('target_id', $userId)
            ->where('status', InviteStatus::PENDING)
            ->with('reservation:id,date')
            ->get()->pluck('reservation.date')
            ->filter()->map(fn ($d) => substr($d, 0, 10))->unique()->values();

        return Inertia::render('reservations/Index', [
            'reservations' => $reservations,
            'authUserId' => $userId,
            'users' => $users,
            'myReservationDates' => $myReservationDates,
            'myRequestDates' => $myRequestDates,
            'myInviteDates' => $myInviteDates,
        ]);
    }

    public function create(): Response
    {
        $tables = Table::where('status', TableStatus::READY)->get();
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('reservations/Create', [
            'tables' => $tables,
            'users' => $users,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ReservationService::createFromWeb($request, auth()->id());

        return redirect()->route('reservations.index');
    }

    public function show(int $id): Response
    {
        $reservation = Reservation::with(['table', 'users'])->findOrFail($id);

        return Inertia::render('reservations/Show', [
            'reservation' => $reservation,
            'authUserId' => auth()->id(),
        ]);
    }

    public function edit(int $id): Response
    {
        $reservation = Reservation::with('table')->findOrFail($id);
        $tables = Table::where('status', TableStatus::READY)
            ->orWhere('id', $reservation->table_id)
            ->get();

        return Inertia::render('reservations/Edit', [
            'reservation' => $reservation,
            'tables' => $tables,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $reservation = Reservation::findOrFail($id);
        ReservationService::update($request, $reservation);

        return redirect()->route('reservations.show', $id);
    }

    public function destroy(int $id): RedirectResponse
    {
        $reservation = Reservation::findOrFail($id);
        ReservationService::softDelete($reservation);

        return redirect()->route('reservations.index');
    }

    public function attachUser(int $id, int $userId): RedirectResponse
    {
        $reservation = Reservation::findOrFail($id);
        abort_unless($reservation->users()->where('user_id', auth()->id())->exists(), 403);
        $reservation->users()->syncWithoutDetaching([$userId]);

        return back();
    }

    public function detachUser(int $id, int $userId): RedirectResponse
    {
        $reservation = Reservation::findOrFail($id);
        abort_unless($reservation->users()->where('user_id', auth()->id())->exists(), 403);
        $reservation->users()->detach($userId);

        return back();
    }
}
