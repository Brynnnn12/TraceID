<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/verify');

Route::get('/verify', [VerificationController::class, 'show'])
    ->middleware('throttle:10,1')
    ->name('verification.show');

Route::post('/verify', [VerificationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('verification.store');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/bank-transfer', [BankTransferController::class, 'edit'])->name('bank-transfer.edit');
    Route::put('/bank-transfer', [BankTransferController::class, 'update'])->name('bank-transfer.update');

    Route::get('/social-media', [SocialMediaController::class, 'edit'])->name('social-media.edit');
    Route::put('/social-media', [SocialMediaController::class, 'update'])->name('social-media.update');

    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');

    Route::get('/verifications', [VerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{verification}', [VerificationController::class, 'detail'])->name('verifications.show');

    Route::get('/verifications/{verification}/photo', [VerificationController::class, 'photo'])
        ->middleware('signed')
        ->name('verification.photo');
});

require __DIR__.'/auth.php';
