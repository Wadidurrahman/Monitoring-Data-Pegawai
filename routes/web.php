<?php

use App\Http\Controllers\CheckUnitController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManualRegistrationController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::post('/cek-unit', [CheckUnitController::class, 'check'])->name('check-unit');
Route::post('/pendaftaran-manual', [ManualRegistrationController::class, 'store'])->name('manual-registration.store');

Route::get('/wilayah/provinsi', [RegionController::class, 'provinces'])->name('regions.provinces');
Route::get('/wilayah/kabupaten-kota', [RegionController::class, 'regencies'])->name('regions.regencies');
Route::get('/wilayah/kecamatan', [RegionController::class, 'districts'])->name('regions.districts');
Route::get('/wilayah/kelurahan', [RegionController::class, 'villages'])->name('regions.villages');

