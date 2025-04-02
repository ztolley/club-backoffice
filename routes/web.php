<?php

use App\Http\Controllers\PublicPlayerContractFormController;
use Illuminate\Support\Facades\Route;

Route::get('/player-contract/signed', function () {
    return view('player-contract-success');
})->name('player.contract.success');
Route::get('/player-contract/{id}', [PublicPlayerContractFormController::class, 'show'])->name('player.contract.show');
Route::post('/player-contract/{id}', [PublicPlayerContractFormController::class, 'submit'])->name('player.contract.submit');
