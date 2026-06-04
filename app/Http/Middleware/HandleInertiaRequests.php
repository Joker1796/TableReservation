<?php

namespace App\Http\Middleware;

use App\Enums\BookingRequestStatus;
use App\Enums\InviteStatus;
use App\Enums\TableStatus;
use App\Models\BookingRequest;
use App\Models\Invite;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'pendingInvites' => fn () => auth()->check()
                ? Invite::with(['author', 'reservation.table'])
                    ->whereHas('reservation')
                    ->where('target_id', auth()->id())
                    ->where('status', InviteStatus::PENDING)
                    ->latest()
                    ->get()
                : [],
            'pendingBookingRequests' => fn () => auth()->check() && auth()->user()->is_admin
                ? BookingRequest::with(['author', 'table'])
                    ->where('status', BookingRequestStatus::PENDING)
                    ->latest()
                    ->get()
                : [],
            'bookingFormData' => fn () => auth()->check() ? [
                'tables' => Table::where('status', TableStatus::READY)->orderBy('name')->get(['id', 'name']),
                'users' => User::where('id', '!=', auth()->id())->orderBy('name')->get(['id', 'name', 'email']),
            ] : null,
        ];
    }
}
