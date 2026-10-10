
<?php

use App\Http\Controllers\CheckUnitController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManualRegistrationController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\RegionController;
use App\Http\Middleware\EnsureMonitoringAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/cek-unit', [CheckUnitController::class, 'check'])->name('check-unit');
Route::post('/pendaftaran-manual', [ManualRegistrationController::class, 'store'])->name('manual-registration.store');

Route::get('/wilayah/provinsi', [RegionController::class, 'provinces'])->name('regions.provinces');
Route::get('/wilayah/kabupaten-kota', [RegionController::class, 'regencies'])->name('regions.regencies');
Route::get('/wilayah/kecamatan', [RegionController::class, 'districts'])->name('regions.districts');
Route::get('/wilayah/kelurahan', [RegionController::class, 'villages'])->name('regions.villages');

Route::get('/monitoring/akses', function (Request $request) {
    $request->session()->regenerate();
    $request->session()->put('monitoring_access_until', now()->addHours((int) config('monitoring.access_hours', 5))->timestamp);

    return redirect()->route('monitoring.index');
})->middleware(['signed', 'throttle:10,1'])->name('monitoring.access');

Route::middleware(EnsureMonitoringAccess::class)->group(function () {
    Route::get('/sabjksabfjsjdbfjksdbfjkbsdfbsdb', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/export/all', [MonitoringController::class, 'exportAll'])->middleware('throttle:2,1')->name('monitoring.export.all');
    Route::get('/monitoring/export/filter', [MonitoringController::class, 'exportFiltered'])->middleware('throttle:2,1')->name('monitoring.export.filtered');

});
