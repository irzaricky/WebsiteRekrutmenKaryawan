<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DataCandidateController;
use App\Http\Middleware\EnsureUserIsCandidate;
use App\Http\Middleware\EnsureUserIsHRD;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Controllers\CandidateRankingController;
use App\Http\Controllers\CandidateUploadController;
use App\Http\Controllers\CandidateProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HRDHistoryController;
use App\Http\Controllers\HRDProfileController;
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

Route::get('/candidate/file/{type}/{filename}', [CandidateUploadController::class, 'getFile'])
    ->name('candidate.file')
    ->middleware('auth'); // Hanya perlu auth



Route::middleware(['auth', EnsureUserIsCandidate::class, EnsureUserIsActive::class])->group(function () {
    // Routes khusus untuk Candidate
    Route::get('/candidate/upload', [CandidateUploadController::class, 'index'])
        ->name('candidate.upload');

    Route::post('/candidate/upload', [CandidateUploadController::class, 'store'])
        ->name('candidate.files.update');

    Route::delete('/candidate/file', [CandidateUploadController::class, 'deleteFile'])
        ->name('candidate.file.delete');
    Route::get('/candidate/file-status', [CandidateUploadController::class, 'fileStatus'])
        ->name('candidate.file-status');
});

Route::middleware(['auth', EnsureUserIsHRD::class, EnsureUserIsActive::class])->group(function () {

    Route::get('/dashboard', function () {
        $controller = new CandidateRankingController();
        return Inertia::render('HRD/Dashboard', $controller->getDashboardAnalytics());
    })->middleware(['auth'])->name('dashboard');
    ;

    Route::get('/hrd/profile', [HRDProfileController::class, 'index'])->name('hrd.profile');
    Route::post('/hrd/profile', [HRDProfileController::class, 'update'])->name('hrd.profile.update');
    Route::get('hrd/profile-image/{filename}', [HRDProfileController::class, 'getProfileImage'])
        ->name('hrd.profile.image');

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

    Route::post('/dashboard/update-file-status', [DataCandidateController::class, 'updateFileStatus'])
        ->name('dashboard.update-file-status');
    Route::get('/dashboard/pending-files', [DataCandidateController::class, 'getPendingFiles'])
        ->name('dashboard.pending-files');
});


Route::middleware(['auth', EnsureUserIsCandidate::class, EnsureUserIsActive::class])->group(function () {
    // Profile routes
    Route::get('/candidate/profile', [CandidateProfileController::class, 'index'])
        ->name('candidate.profile');
    Route::post('/candidate/profile', [CandidateUploadController::class, 'store_profile'])
        ->name('candidate.profile.update');

    // File upload routes  
    Route::get('/candidate/upload', [CandidateUploadController::class, 'index'])
        ->name('candidate.upload');
    Route::post('/candidate/upload', [CandidateUploadController::class, 'store_files'])
        ->name('candidate.files.update');
    Route::delete('/candidate/file', [CandidateUploadController::class, 'deleteFile'])
        ->name('candidate.file.delete');
    Route::get('/candidate/file-status', [CandidateUploadController::class, 'fileStatus'])
        ->name('candidate.file-status');
});

Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


// Catch-all route for undefined routes
Route::get('/{any}', function () {
    return Inertia::render('Not-found'); // Render the 404 component
})->where('any', '.*');