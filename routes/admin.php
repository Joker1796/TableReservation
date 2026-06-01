<?php

use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminReservationRequestController;
use App\Http\Controllers\Admin\AdminTableController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Tables
        Route::get('/tables', [AdminTableController::class, 'index'])
            ->name('tables.index');
        Route::get('/tables/create', [AdminTableController::class, 'create'])
            ->name('tables.create');
        Route::post('/tables', [AdminTableController::class, 'store'])
            ->name('tables.store');
        Route::get('/tables/{id}/edit', [AdminTableController::class, 'edit'])
            ->name('tables.edit');
        Route::put('/tables/{id}', [AdminTableController::class, 'update'])
            ->name('tables.update');
        Route::delete('/tables/{id}', [AdminTableController::class, 'destroy'])
            ->name('tables.destroy');

        // Reservations
        Route::get('/reservations', [AdminReservationController::class, 'index'])
            ->name('reservations.index');
        Route::post('/reservations/{id}/invite', [AdminReservationController::class, 'sendInvite'])
            ->name('reservations.sendInvite');
        Route::delete('/reservations/{id}', [AdminReservationController::class, 'destroy'])
            ->name('reservations.destroy');

        // Reservation Requests
        Route::get('/requests', [AdminReservationRequestController::class, 'index'])
            ->name('requests.index');
        Route::put('/requests/{id}/status', [AdminReservationRequestController::class, 'updateStatus'])
            ->name('requests.updateStatus');
        Route::put('/requests/{id}/table', [AdminReservationRequestController::class, 'assignTable'])
            ->name('requests.assignTable');
        Route::delete('/requests/{id}', [AdminReservationRequestController::class, 'destroy'])
            ->name('requests.destroy');
    });
