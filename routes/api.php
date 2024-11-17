<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::get('', [UserController::class, 'index']);
    Route::put('update/{id}', [UserController::class, 'update']);
    Route::delete('delete/{id}', [UserController::class, 'destroy']);

    Route::get('/peserta', [PesertaController::class, 'index']);
    Route::post('/peserta/create', [PesertaController::class, 'store']);
    Route::post('/peserta/update/{id}', [PesertaController::class, 'update']);
    Route::delete('/peserta/delete/{id}', [PesertaController::class, 'destroy']);

    Route::get('/event', [EventController::class, 'index']);
    Route::post('/event/create', [EventController::class, 'store']);
    Route::post('/event/update/{id}', [EventController::class, 'update']);
    Route::delete('/event/delete/{id}', [EventController::class, 'destroy']);
    Route::get('/events/search/{id}', [EventController::class, 'search']);

    Route::get('/sponsors', [SponsorController::class, 'index']);
    Route::post('/sponsors/create', [SponsorController::class, 'store']);
    Route::post('/sponsors/update/{id}', [SponsorController::class, 'update']);
    Route::delete('/sponsors/delete/{id}', [SponsorController::class, 'destroy']);
    Route::get('/sponsors/search/{nama_sponsor}', [SponsorController::class, 'search']);

    Route::post('/logout', [UserController::class, 'logout']);
});
