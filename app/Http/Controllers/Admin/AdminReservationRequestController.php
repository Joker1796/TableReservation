<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservationRequest;
use App\Models\Table;
use App\Services\ReservationRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReservationRequestController extends Controller
{
    public function index(): Response
    {
        $requests = ReservationRequest::with(['author', 'table'])
            ->latest()
            ->get();

        $tables = Table::orderBy('name')->get(['id', 'name', 'status']);

        return Inertia::render('admin/requests/Index', [
            'requests' => $requests,
            'tables' => $tables,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        ReservationRequestService::updateStatus($request, ReservationRequest::findOrFail($id), auth()->id());

        return redirect()->route('admin.requests.index');
    }

    public function assignTable(Request $request, int $id): RedirectResponse
    {
        ReservationRequestService::assignTable($request, ReservationRequest::findOrFail($id));

        return redirect()->route('admin.requests.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        ReservationRequestService::softDelete(ReservationRequest::findOrFail($id));

        return redirect()->route('admin.requests.index');
    }
}
