<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk menampilkan halaman dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Route untuk mengambil data grafik & KPI (Bisa dipanggil via AJAX)
Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');