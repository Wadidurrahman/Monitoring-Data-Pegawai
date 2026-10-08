<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\ManualRegistration;
use App\Models\MasterEmployee;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = MasterEmployee::query()
            ->selectRaw("
                COUNT(DISTINCT nik_lookup) AS total,
                COUNT(DISTINCT CASE
                    WHEN REGEXP_REPLACE(
                        LOWER(TRIM(COALESCE(respSE26_keberadaan_klrg, ''))),
                        '^[0-9]+[.][[:space:]]*',
                        ''
                    ) IN ('ditemukan', 'baru')
                    THEN nik_lookup
                END) AS sudah_didata
            ")
            ->first();

        $total = (int) ($stats->total ?? 0);
        $sudahDidata = (int) ($stats->sudah_didata ?? 0);
        $belumDidata = $total - $sudahDidata;
        $manual = ManualRegistration::count();
        $totalBarisMaster = MasterEmployee::count();

        $institutions = Institution::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'alias']);

        return view('dashboard', compact(
            'total',
            'sudahDidata',
            'belumDidata',
            'manual',
            'totalBarisMaster',
            'institutions'
        ));
    }
}
