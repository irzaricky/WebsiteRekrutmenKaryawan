<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DataCandidateController;
use App\Http\Middleware\EnsureUserIsHRD;
use App\Http\Controllers\CandidateRankingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HRDHistoryController;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('LandingPage', [
        'title' => "Home",
    ]);
})->name('home');

Route::get('/company', function () {
    return Inertia::render('Landing-subpages/CompanyPage', [
        'title' => "Company",
    ]);
});

Route::get('/features', function () {
    return Inertia::render('Landing-subpages/FeaturesPage', [
        'title' => "Features",
    ]);
});

Route::get('/product', function () {
    return Inertia::render('Landing-subpages/ProductPage', [
        'title' => "Product",
    ]);
});

Route::middleware(['auth', EnsureUserIsHRD::class])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'title' => "Dashboard",
        ]);
    })->name('dashboard');

    Route::get('/dashboard/data-candidate', [DataCandidateController::class, 'getUser'])->name('dashboard.data-candidate');

    Route::put('/dashboard/data-candidate/{id}', [DataCandidateController::class, 'update'])->name('dashboard.data-candidate-put');

    Route::get('/edit-data-candidate/{id}', [DataCandidateController::class, 'edit'])->name('dashboard.edit-data-candidate');

    Route::get('/dashboard/history', [HRDHistoryController::class, 'index'])->name('dashboard.history');

    Route::get('/dashboard/ranking', [CandidateRankingController::class, 'calculateRanking'])->name('dashboard.ranking');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// Catch-all route for undefined routes
Route::get('/{any}', function () {
    return Inertia::render('Not-found'); // Render the 404 component
})->where('any', '.*');