<?php

use App\Http\Controllers\InviteController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationRequestController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('reservation')
    ->name('reservation.')
    ->group(function () {
        Route::get('/create', [ReservationController::class, 'create']);
        Route::get('/{reservation}', [ReservationController::class, 'show']);
        Route::put('/{reservation}', [ReservationController::class, 'update']);
        Route::delete('/{reservation}', [ReservationController::class, 'softDelete']);
        Route::put('/{reservation}/user/{user}', [ReservationController::class, 'attachUser']);
        Route::delete('/{reservation}/user/{user}', [ReservationController::class, 'detachUser']);
    });

Route::middleware(['auth', 'verified'])
    ->prefix('reservation-request')
    ->name('reservation-request.')
    ->group(function () {
        Route::get('/create', [ReservationRequestController::class, 'create']);
        Route::get('/{reservationRequest}', [ReservationRequestController::class, 'show']);
        Route::put('/{reservationRequest}', [ReservationRequestController::class, 'update']);
        Route::delete('/{reservationRequest}', [ReservationRequestController::class, 'softDelete']);
        Route::put('/{reservationRequest}/user/{user}', [ReservationRequestController::class, 'attachUser']);
        Route::delete('/{reservationRequest}/user/{user}', [ReservationRequestController::class, 'detachUser']);
        Route::put('/{reservationRequest}/table/{table}', [ReservationRequestController::class, 'associateTable']);
        Route::delete('/{reservationRequest}/table', [ReservationRequestController::class, 'deleteTable']);
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
        Route::put('/{table}/reservation-request/{rr}', [TableController::class, 'addReservationRequest']);
        Route::delete('/{table}/reservation-request/{rr}', [TableController::class, 'deleteReservationRequest']);
    });

Route::middleware('auth:sanctum')
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/', fn (Request $request) => $request->user());
        Route::put('/{user}/reservation/{reservation}', [UserController::class, 'attachReservation']);
        Route::delete('/{user}/reservation/{reservation}', [UserController::class, 'detachReservation']);
        Route::put('/{user}/reservation-request/{reservationRequest}', [UserController::class, 'attachReservationRequest']);
        Route::delete('/{user}/reservation-request/{reservationRequest}', [UserController::class, 'detachReservationRequest']);
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
