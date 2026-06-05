<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\Table;
use App\Services\BookingRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminBookingRequestController extends Controller
{
    public function index(): Response
    {
        $requests = BookingRequest::with(['author', 'table'])
            ->latest()
            ->paginate(10);

        $tables = Table::orderBy('name')->get(['id', 'name', 'status']);

        return Inertia::render('admin/requests/Index', [
            'requests' => $requests,
            'tables' => $tables,
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        BookingRequestService::updateStatus($request, BookingRequest::findOrFail($id), auth()->id());

        return redirect()->route('admin.requests.index');
    }

    public function assignTable(Request $request, int $id): RedirectResponse
    {
        BookingRequestService::assignTable($request, BookingRequest::findOrFail($id));

        return redirect()->route('admin.requests.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        BookingRequestService::softDelete(BookingRequest::findOrFail($id));

        return redirect()->route('admin.requests.index');
    }
}
