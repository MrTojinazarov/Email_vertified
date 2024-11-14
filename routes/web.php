<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/email-test', [TestController::class, 'index'])->name('email-test');
    Route::post('/send-message/{user}', [TestController::class, 'create'])->name('send-message');
    Route::get('/send-message', [TestController::class, 'showVerificationPage'])->name('verification.page');
    Route::post('/send-message', [TestController::class, 'verifyCode'])->name('verification.code');
});

require __DIR__ . '/auth.php';
