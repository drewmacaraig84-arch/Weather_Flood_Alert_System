<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAlertController;
use App\Http\Controllers\AdminAlertController;

// Public & Student Mobile Routes
Route::get('/', [StudentAlertController::class, 'index'])->name('student.index');
Route::post('/report-flood', [StudentAlertController::class, 'storeReport'])->name('student.report');
Route::get('/api/map-data', [StudentAlertController::class, 'mapData'])->name('api.mapData');

// School Administrator Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminAlertController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/class-status', [AdminAlertController::class, 'updateStatus'])->name('admin.status.update');
    Route::post('/announcements', [AdminAlertController::class, 'storeAnnouncement'])->name('admin.announcements.store');
    Route::delete('/announcements/{id}', [AdminAlertController::class, 'destroyAnnouncement'])->name('admin.announcements.destroy');
    Route::post('/reports/{id}/status', [AdminAlertController::class, 'updateReportStatus'])->name('admin.reports.status');
    Route::post('/zones', [AdminAlertController::class, 'storeZone'])->name('admin.zones.store');
    Route::delete('/zones/{id}', [AdminAlertController::class, 'destroyZone'])->name('admin.zones.destroy');
});
