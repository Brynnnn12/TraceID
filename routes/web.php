<?php

use App\Http\Controllers\CaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/verify/{token}', [VerificationController::class, 'show'])
    ->middleware('throttle:20,1')
    ->name('verification.show');

Route::post('/verify/{token}', [VerificationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('verification.store');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/verifications/{verification}/photo', [VerificationController::class, 'photo'])
        ->middleware('signed')
        ->name('verification.photo');

    Route::resource('cases', CaseController::class);
});

require __DIR__.'/auth.php';
