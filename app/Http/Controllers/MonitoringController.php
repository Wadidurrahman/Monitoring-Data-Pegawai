<?php

namespace App\Http\Controllers;

use App\Exports\MonitoringExport;
use App\Models\Institution;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Semua data langsung dikirim ke halaman
        $employees = MasterEmployee::query()
            ->orderBy('id')
            ->limit(50)
            ->get();

        // Data instansi
        $institutions = Institution::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Kecamatan
        $kecamatans = MasterEmployee::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        // Kelurahan
        $kelurahans = MasterEmployee::whereNotNull('kelurahan')
            ->where('kelurahan', '!=', '')
            ->distinct()
            ->orderBy('kelurahan')
            ->pluck('kelurahan');

        // Statistik
        $totalData = $employees->count();

        $sudahDidata = $employees
            ->where('sudah_didata', 'Sudah Didata')
            ->count();

        $belumDidata = $employees
            ->where('sudah_didata', 'Belum Didata')
            ->count();

        $persentase = $totalData > 0
            ? round(($sudahDidata / $totalData) * 100)
            : 0;

        return view('monitoring.index', compact(
            'employees',
            'institutions',
            'kecamatans',
            'kelurahans',
            'totalData',
            'sudahDidata',
            'belumDidata',
            'persentase'
        ));
    }

    public function exportAll()
    {
        return Excel::download(
            new MonitoringExport(),
            'monitoring_semua_data_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportFiltered(Request $request)
    {
        $filters = $request->only([
            'search',
            'institution',
            'status',
            'kecamatan',
            'kelurahan',
        ]);

        return Excel::download(
            new MonitoringExport($filters),
            'monitoring_data_filter_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
