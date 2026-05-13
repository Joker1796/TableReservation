<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/reservation/create', [ReservationController::class, 'create']);
Route::get('/reservation/{reservation}', [ReservationController::class, 'show']);
Route::put('/reservation/{reservation}', [ReservationController::class, 'update']);
Route::delete('/reservation/{reservation}', [ReservationController::class, 'softDelete']);
Route::put('/reservation/{reservation}/user/{user}', [ReservationController::class, 'attachUser']);
Route::delete('/reservation/{reservation}/user/{user}', [ReservationController::class, 'detachUser']);

Route::put('/user/{user}/reservation/{reservation}', [UserController::class, 'attachReservation']);
Route::delete('/user/{user}/reservation/{reservation}', [UserController::class, 'detachReservation']);
