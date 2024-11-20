<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DataCandidateController;
use App\Http\Middleware\EnsureUserIsCandidate;
use App\Http\Middleware\EnsureUserIsHRD;
use App\Http\Controllers\CandidateRankingController;
use App\Http\Controllers\CandidateUploadController;
use App\Http\Controllers\CandidateProfileController;
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

// Pindahkan rute file di luar group middleware
Route::get('/candidate/file/{type}/{filename}', [CandidateUploadController::class, 'getFile'])
    ->name('candidate.file')
    ->middleware('auth'); // Hanya perlu auth

Route::middleware(['auth', EnsureUserIsCandidate::class])->group(function () {
    // Routes khusus untuk Candidate
    Route::get('/candidate/upload', [CandidateUploadController::class, 'index'])
        ->name('candidate.upload');

    Route::post('/candidate/upload', [CandidateUploadController::class, 'store'])
        ->name('candidate.upload.store');

    Route::delete('/candidate/file', [CandidateUploadController::class, 'deleteFile'])
        ->name('candidate.file.delete');
});

Route::middleware(['auth', EnsureUserIsHRD::class])->group(function () {

    Route::get('/dashboard', function () {
        $controller = new CandidateRankingController();
        return Inertia::render('Dashboard', $controller->getDashboardAnalytics());
    })->middleware(['auth'])->name('dashboard');
    ;

    //menampilkan data kandidat
    Route::get('/dashboard/data-candidate', [DataCandidateController::class, 'getUser'])->name('dashboard.data-candidate');

    //menghapus data kandidat
    Route::get('/edit-data-candidate/{id}', [DataCandidateController::class, 'edit'])->name('dashboard.edit-data-candidate');

    //mengupdate data kandidat
    Route::put('/dashboard/data-candidate/{id}', [DataCandidateController::class, 'update'])->name('dashboard.data-candidate-put');

    //menampilkan data kandidat
    Route::get('/dashboard/history', [HRDHistoryController::class, 'index'])->name('dashboard.history');

    //menampilkan data ranking
    Route::get('/dashboard/ranking', [CandidateRankingController::class, 'calculateRanking'])->name('dashboard.ranking');

    Route::get('/dashboard/candidate-details/{id}', [DataCandidateController::class, 'show'])
        ->name('dashboard.candidate-details');
});

Route::middleware(['auth', EnsureUserIsCandidate::class])->group(function () {
    // Profile routes
    Route::controller(CandidateProfileController::class)->group(function () {
        Route::get('/profile/candidate', 'index')->name('profile.candidate.show');
        Route::post('/profile/candidate', 'update')->name('profile.candidate.update');
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