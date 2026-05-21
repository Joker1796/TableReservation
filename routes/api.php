<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationRequestController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    // TODO вынести в сервис через контроллер для единообразия кода
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/reservation/create', [ReservationController::class, 'create']);
    Route::get('/reservation/{reservation}', [ReservationController::class, 'show']);
    Route::put('/reservation/{reservation}', [ReservationController::class, 'update']);
    Route::delete('/reservation/{reservation}', [ReservationController::class, 'softDelete']);
    Route::put('/reservation/{reservation}/user/{user}', [ReservationController::class, 'attachUser']);
    Route::delete('/reservation/{reservation}/user/{user}', [ReservationController::class, 'detachUser']);

    Route::get('/reservation-request/create', [ReservationRequestController::class, 'create']);
    Route::get('/reservation-request/{reservationRequest}', [ReservationRequestController::class, 'show']);
    Route::put('/reservation-request/{reservationRequest}', [ReservationRequestController::class, 'update']);
    Route::delete('/reservation-request/{reservationRequest}', [ReservationRequestController::class, 'softDelete']);
    Route::put(
        '/reservation-request/{reservationRequest}/user/{user}',
        [ReservationRequestController::class, 'attachUser']
    );
    Route::delete(
        '/reservation-request/{reservationRequest}/user/{user}',
        [ReservationRequestController::class, 'detachUser']
    );
    Route::put(
        '/reservation-request/{reservationRequest}/table/{table}',
        [ReservationRequestController::class, 'associateTable']
    );
    Route::delete(
        '/reservation-request/{reservationRequest}/table',
        [ReservationRequestController::class, 'deleteTable']
    );

    Route::get('/table/create', [TableController::class, 'create']);
    Route::get('/table/{table}', [TableController::class, 'show']);
    Route::put('/table/{table}', [TableController::class, 'update']);
    Route::delete('/table/{table}', [TableController::class, 'softDelete']);
    Route::put('/table/{table}/reservation/{reservation}', [TableController::class, 'addReservation']);
    Route::delete('/table/{table}/reservation/{reservation}', [TableController::class, 'deleteReservation']);
    Route::put('/table/{table}/reservation-request/{reservationRequest}', [TableController::class, 'addReservationRequest']);
    Route::delete('/table/{table}/reservation-request/{reservationRequest}', [TableController::class, 'deleteReservationRequest']);

    Route::put('/user/{user}/reservation/{reservation}', [UserController::class, 'attachReservation']);
    Route::delete('/user/{user}/reservation/{reservation}', [UserController::class, 'detachReservation']);
    Route::put('/user/{user}/reservation-request/{reservationRequest}', [UserController::class, 'attachReservationRequest']);
    Route::delete('/user/{user}/reservation-request/{reservationRequest}', [UserController::class, 'detachReservationRequest']);
});
