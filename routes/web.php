<?php

use App\Http\Controllers\Web\BookingRequestWebController;
use App\Http\Controllers\Web\FeedController;
use App\Http\Controllers\Web\InviteWebController;
use App\Http\Controllers\Web\WorkshopController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', ['canRegister' => Features::enabled(Features::registration())])
    ->name('home');

Route::get('/workshop', [WorkshopController::class, 'index'])->name('workshop');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');
    Route::post('booking-requests', [BookingRequestWebController::class, 'store'])
        ->name('booking-requests.store');
    Route::put('invites/{id}/accept', [InviteWebController::class, 'accept'])
        ->name('invites.accept');
    Route::put('invites/{id}/reject', [InviteWebController::class, 'reject'])
        ->name('invites.reject');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/reservations.php';
