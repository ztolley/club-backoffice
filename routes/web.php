<?php

use App\Http\Controllers\PublicPlayerApplicationFormController;
use App\Http\Controllers\PublicPlayerContractFormController;
use App\Http\Controllers\PublicPlayerSignupFormController;
use Illuminate\Support\Facades\Route;

Route::get('/player-application/success', function () {
    return view('application/success');
})->name('application.success');
Route::get('/player-application', [PublicPlayerApplicationFormController::class, 'show'])->name('application.show');
Route::post('/player-application', [PublicPlayerApplicationFormController::class, 'submit'])->name('application.submit');

Route::get('/player-contract/signed', function () {
    return view('player/contract/success');
})->name('player.contract.success');
Route::get('/player-contract/{id}', [PublicPlayerContractFormController::class, 'show'])->name('player.contract.show');
Route::post('/player-contract/{id}', [PublicPlayerContractFormController::class, 'submit'])->name('player.contract.submit');

Route::get('/player-signup/complete', function () {
    return view('player/signup/success');
})->name('player.signup.success');
Route::get('/player-signup', [PublicPlayerSignupFormController::class, 'show'])->name('player.signup.show');
Route::post('/player-signup', [PublicPlayerSignupFormController::class, 'submit'])->name('player.signup.submit');
