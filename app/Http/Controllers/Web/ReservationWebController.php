<?php

namespace App\Http\Controllers\Web;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
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
    public function index(): Response
    {
        $reservations = Reservation::with(['table', 'users'])->latest()->get();

        return Inertia::render('reservations/Index', [
            'reservations' => $reservations,
            'authUserId' => auth()->id(),
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
        $reservation = auth()->user()->reservations()->with('table')->findOrFail($id);
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
        $reservation = auth()->user()->reservations()->findOrFail($id);
        ReservationService::update($request, $reservation);

        return redirect()->route('reservations.show', $id);
    }

    public function destroy(int $id): RedirectResponse
    {
        $reservation = auth()->user()->reservations()->findOrFail($id);
        ReservationService::softDelete($reservation);

        return redirect()->route('reservations.index');
    }
}
