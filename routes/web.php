<?php

use App\Http\Controllers\Web\BookingRequestWebController;
use App\Http\Controllers\Web\EventRegistrationController;
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
    Route::post('events/{id}/register', [EventRegistrationController::class, 'store'])
        ->name('events.register');
    Route::delete('events/{id}/register', [EventRegistrationController::class, 'destroy'])
        ->name('events.unregister');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::post('admin/events/registrations/seen', [EventRegistrationController::class, 'markSeen'])
        ->name('admin.events.registrations.seen');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/reservations.php';
