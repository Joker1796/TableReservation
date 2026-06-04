<?php

namespace App\Http\Controllers\Web;

use App\Enums\TableStatus;
use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\Table;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $tables = Table::where('status', TableStatus::READY)->get();

        $myRequests = BookingRequest::with(['table', 'users'])
            ->where('author_id', auth()->id())
            ->latest()
            ->get();

        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('Dashboard', [
            'tables' => $tables,
            'myRequests' => $myRequests,
            'users' => $users,
        ]);
    }
}
