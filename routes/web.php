<?php

use App\Http\Controllers\PublicPlayerContractFormController;
use App\Http\Controllers\PublicPlayerApplicationFormController;
use Illuminate\Support\Facades\Route;

Route::get('/player-application/success', function () {
    return view('player-application-success');
})->name('player.application.success');
Route::get('/player-application', [PublicPlayerApplicationFormController::class, 'show'])->name('player.application.show');
Route::post('/player-application', [PublicPlayerApplicationFormController::class, 'submit'])->name('player.application.submit');

Route::get('/player-contract/signed', function () {
    return view('player-contract-success');
})->name('player.contract.success');
Route::get('/player-contract/{id}', [PublicPlayerContractFormController::class, 'show'])->name('player.contract.show');
Route::post('/player-contract/{id}', [PublicPlayerContractFormController::class, 'submit'])->name('player.contract.submit');
