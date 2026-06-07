<?php

use App\Http\Controllers\Admin\AdminBookingRequestController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminTableController;
use App\Http\Controllers\Admin\AdminWorkshopController;
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

        // Booking Requests
        Route::get('/requests', [AdminBookingRequestController::class, 'index'])
            ->name('requests.index');
        Route::put('/requests/{id}/status', [AdminBookingRequestController::class, 'updateStatus'])
            ->name('requests.updateStatus');
        Route::put('/requests/{id}/table', [AdminBookingRequestController::class, 'assignTable'])
            ->name('requests.assignTable');
        Route::delete('/requests/{id}', [AdminBookingRequestController::class, 'destroy'])
            ->name('requests.destroy');

        // Workshop
        Route::get('/workshop', [AdminWorkshopController::class, 'index'])
            ->name('workshop.index');
        Route::post('/workshop', [AdminWorkshopController::class, 'store'])
            ->name('workshop.store');
        Route::delete('/workshop/{id}', [AdminWorkshopController::class, 'destroy'])
            ->name('workshop.destroy');

        // Posts
        Route::get('/posts', [AdminPostController::class, 'index'])
            ->name('posts.index');
        Route::get('/posts/create', [AdminPostController::class, 'create'])
            ->name('posts.create');
        Route::post('/posts', [AdminPostController::class, 'store'])
            ->name('posts.store');
        Route::get('/posts/{id}/edit', [AdminPostController::class, 'edit'])
            ->name('posts.edit');
        Route::put('/posts/{id}', [AdminPostController::class, 'update'])
            ->name('posts.update');
        Route::delete('/posts/{id}', [AdminPostController::class, 'destroy'])
            ->name('posts.destroy');
    });
