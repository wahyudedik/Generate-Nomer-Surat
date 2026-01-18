<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterActivityLogController;
use App\Http\Controllers\LetterFormatController;
use App\Http\Controllers\LetterInController;
use App\Http\Controllers\LetterOutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
    // redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin|staff'])->group(function () {
    Route::get('/letters/out', [LetterOutController::class, 'index'])->name('letters.out.index');
    Route::post('/letters/out/generate', [LetterOutController::class, 'generate'])->name('letters.out.generate');
    Route::get('/letters/out/{letter}/edit', [LetterOutController::class, 'edit'])->name('letters.out.edit');
    Route::put('/letters/out/{letter}', [LetterOutController::class, 'update'])->name('letters.out.update');

    Route::get('/letters/in', [LetterInController::class, 'index'])->name('letters.in.index');
    Route::get('/letters/in/create', [LetterInController::class, 'create'])->name('letters.in.create');
    Route::post('/letters/in', [LetterInController::class, 'store'])->name('letters.in.store');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('letter-formats', LetterFormatController::class)->except(['show']);
    Route::get('/letters/logs', [LetterActivityLogController::class, 'index'])->name('letters.logs.index');
});

require __DIR__ . '/auth.php';
