<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ComplaintManageController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

// =====================
// PUBLIC ROUTES
// =====================

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Peta publik (bisa dilihat tanpa login)
Route::get('/peta', [MapController::class, 'public'])->name('map.public');

// API: ambil data GeoJSON untuk peta
Route::get('/api/complaints/geojson', [MapController::class, 'geojson'])->name('api.geojson');

// =====================
// USER ROUTES (login required)
// =====================

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard user
    Route::get('/dashboard', [ComplaintController::class, 'dashboard'])->name('dashboard');

    // CRUD Pengaduan
    Route::resource('complaints', ComplaintController::class);

    // Rating & ulasan pengaduan (hanya untuk yang sudah selesai)
    Route::post('/complaints/{complaint}/rating', [\App\Http\Controllers\ComplaintRatingController::class, 'store'])
        ->name('complaints.rating.store');

    // Profile (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================
// ADMIN ROUTES
// =====================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/complaints', [ComplaintManageController::class, 'index'])->name('complaints.index');

    // Export (ditaruh SEBELUM route {complaint} supaya tidak bentrok)
    Route::get('/complaints/export/csv', [ComplaintManageController::class, 'exportCsv'])->name('complaints.export.csv');
    Route::get('/complaints/export/pdf', [ComplaintManageController::class, 'exportPdf'])->name('complaints.export.pdf');

    Route::get('/complaints/{complaint}', [ComplaintManageController::class, 'show'])->name('complaints.show');
    Route::patch('/complaints/{complaint}/status', [ComplaintManageController::class, 'updateStatus'])->name('complaints.updateStatus');
    Route::get('/map', [MapController::class, 'admin'])->name('map');
});

require __DIR__.'/auth.php';