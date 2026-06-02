<?php

use App\Http\Controllers\Web\ReservationWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('reservations')
    ->name('reservations.')
    ->group(function () {
        Route::get('/create', [ReservationWebController::class, 'create'])
            ->name('create');
        Route::post('/', [ReservationWebController::class, 'store'])
            ->name('store');
        Route::get('/{id}/edit', [ReservationWebController::class, 'edit'])
            ->name('edit');
        Route::put('/{id}', [ReservationWebController::class, 'update'])
            ->name('update');
        Route::delete('/{id}', [ReservationWebController::class, 'destroy'])
            ->name('destroy');
    });

Route::middleware(['auth', 'verified'])
    ->prefix('reservations')
    ->name('reservations.')
    ->group(function () {
        Route::get('/', [ReservationWebController::class, 'index'])
            ->name('index');
        Route::get('/{id}', [ReservationWebController::class, 'show'])
            ->name('show');
        Route::put('/{id}/user/{userId}', [ReservationWebController::class, 'attachUser'])
            ->name('user.attach');
        Route::delete('/{id}/user/{userId}', [ReservationWebController::class, 'detachUser'])
            ->name('user.detach');
    });
