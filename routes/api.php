<?php

use App\Http\Controllers\API\BookingRequestController;
use App\Http\Controllers\API\FeedController;
use App\Http\Controllers\API\InviteController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\TableController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/feed', [FeedController::class, 'index']);
    });

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('reservation')
    ->name('reservation.')
    ->group(function () {
        Route::get('/create', [ReservationController::class, 'create']);
        Route::put('/{reservation}', [ReservationController::class, 'update']);
        Route::delete('/{reservation}', [ReservationController::class, 'softDelete']);
    });

Route::middleware(['auth', 'verified'])
    ->prefix('reservation')
    ->name('reservation.')
    ->group(function () {
        Route::get('/{reservation}', [ReservationController::class, 'show']);
        Route::put('/{reservation}/user/{user}', [ReservationController::class, 'attachUser']);
        Route::delete('/{reservation}/user/{user}', [ReservationController::class, 'detachUser']);
    });

Route::middleware(['auth', 'verified'])
    ->prefix('booking-request')
    ->name('booking-request.')
    ->group(function () {
        Route::get('/create', [BookingRequestController::class, 'create']);
        Route::get('/{bookingRequest}', [BookingRequestController::class, 'show']);
        Route::put('/{bookingRequest}', [BookingRequestController::class, 'update']);
        Route::delete('/{bookingRequest}', [BookingRequestController::class, 'softDelete']);
        Route::put('/{bookingRequest}/user/{user}', [BookingRequestController::class, 'attachUser']);
        Route::delete('/{bookingRequest}/user/{user}', [BookingRequestController::class, 'detachUser']);
        Route::put('/{bookingRequest}/table/{table}', [BookingRequestController::class, 'associateTable']);
        Route::delete('/{bookingRequest}/table', [BookingRequestController::class, 'deleteTable']);
    });

Route::middleware('auth:sanctum')
    ->prefix('table')
    ->name('table.')
    ->group(function () {
        Route::get('/create', [TableController::class, 'create']);
        Route::get('/{table}', [TableController::class, 'show']);
        Route::put('/{table}', [TableController::class, 'update']);
        Route::delete('/{table}', [TableController::class, 'softDelete']);
        Route::put('/{table}/reservation/{reservation}', [TableController::class, 'addReservation']);
        Route::delete('/{table}/reservation/{reservation}', [TableController::class, 'deleteReservation']);
        Route::put('/{table}/booking-request/{br}', [TableController::class, 'addBookingRequest']);
        Route::delete('/{table}/booking-request/{br}', [TableController::class, 'deleteBookingRequest']);
    });

Route::middleware('auth:sanctum')
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/', fn (Request $request) => $request->user());
        Route::put('/{user}/reservation/{reservation}', [UserController::class, 'attachReservation']);
        Route::delete('/{user}/reservation/{reservation}', [UserController::class, 'detachReservation']);
        Route::put('/{user}/booking-request/{bookingRequest}', [UserController::class, 'attachBookingRequest']);
        Route::delete('/{user}/booking-request/{bookingRequest}', [UserController::class, 'detachBookingRequest']);
    });

Route::middleware('auth:sanctum')
    ->prefix('invite')
    ->name('invite.')
    ->group(function () {
        Route::get('/create', [InviteController::class, 'create']);
        Route::get('/{invite}', [InviteController::class, 'show']);
        Route::put('/{invite}/status/{status}', [InviteController::class, 'setStatus']);
        Route::put('/{invite}/accept', [InviteController::class, 'accept']);
        Route::put('/{invite}/revoke', [InviteController::class, 'revoke']);
        Route::delete('/{invite}', [InviteController::class, 'softDelete']);
    });
