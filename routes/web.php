<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard-tail', function () {
    return view('./dashboard/dashboard-tail');
})->name('dashboard-tail');

Route::get('/dashboard-tukUI', function () {
    return view('./dashboard/dashboard-tukUI');
})->name('dashboard-tukUI');

Route::get('/dashboard-vanila', function () {
    return view('./dashboard/dashboard-vanila');
})->name('dashboard-vanila');

Route::get('/dashboard', function () {
    return view('/dashboard/dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__ . '/auth.php';
