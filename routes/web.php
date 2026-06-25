<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Redirect root to dashboard global
Route::get('/', function () {
    return redirect()->route('dashboard.global');
});

// Route untuk menampilkan halaman dashboard Global
Route::get('/dashboard/global', [DashboardController::class, 'global'])->name('dashboard.global');

// Route untuk menampilkan halaman dashboard Indonesia
Route::get('/dashboard/indonesia', [DashboardController::class, 'indonesia'])->name('dashboard.indonesia');

// Route untuk mengambil data grafik & KPI via AJAX
Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');