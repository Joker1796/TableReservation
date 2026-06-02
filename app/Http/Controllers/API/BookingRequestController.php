<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\Table;
use App\Models\User;
use App\Services\BookingRequestService;
use Illuminate\Http\Request;

class BookingRequestController extends Controller
{
    public function create(Request $request)
    {
        return BookingRequestService::create($request);
    }

    public function show(BookingRequest $bookingRequest)
    {
        return BookingRequestService::show($bookingRequest);
    }

    public function update(Request $request, BookingRequest $bookingRequest)
    {
        return BookingRequestService::update($request, $bookingRequest);
    }

    public function softDelete(BookingRequest $bookingRequest)
    {
        return BookingRequestService::softDelete($bookingRequest);
    }

    public function attachUser(BookingRequest $bookingRequest, User $user)
    {
        return BookingRequestService::attachUser($bookingRequest, $user);
    }

    public function detachUser(BookingRequest $bookingRequest, User $user)
    {
        return BookingRequestService::detachUser($bookingRequest, $user);
    }

    public function associateTable(BookingRequest $bookingRequest, Table $table)
    {
        return BookingRequestService::associateTable($bookingRequest, $table);
    }

    public function deleteTable(BookingRequest $bookingRequest)
    {
        return BookingRequestService::deleteTable($bookingRequest);
    }
}
