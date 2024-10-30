<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserIsHRD;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

    Route::get('/dashboard/data-candidate', function () {
        return Inertia::render('SubDashboard/data-candidate', [
            'title' => "Data Candidate",
        ]);
    });

    Route::get('/dashboard/edit-data-candidate', function () {
        return Inertia::render('SubDashboard/data-candidate', [
            'title' => "Data Karyawan",
        ]);
    });
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