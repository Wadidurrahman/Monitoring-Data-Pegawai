<?php

namespace App\Http\Controllers;

use App\Exports\MonitoringExport;
use App\Models\Institution;
use App\Services\NikSecurityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class MonitoringController extends Controller
{
    private const PAGE_SIZE = 50;

    private const SUDAH_SQL = "(LOWER(TRIM(COALESCE(status_didata, ''))) IN ('sudah didata', 'sudah', 'ditemukan', 'baru', '1') OR REGEXP_REPLACE(LOWER(TRIM(COALESCE(respSE26_keberadaan_klrg, ''))), '^[0-9]+[.][[:space:]]*', '') IN ('ditemukan', 'baru'))";

    public function index(Request $request, NikSecurityService $nik): View|JsonResponse
    {
        $filters = $request->validate([
            'data' => ['sometimes', 'in:1'],
            'search' => ['nullable', 'string', 'max:100'],
            'institution' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:Sudah Didata,Belum Didata,Perlu Tindak Lanjut'],
            'source' => ['nullable', 'in:all,master,manual'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'phase' => ['nullable', 'in:belum,sudah,manual'],
            'after' => ['nullable', 'integer', 'min:0'],
        ]);

        $page = $this->fetchPage($filters, $nik);

        if ($request->query('data') === '1') {
            return response()->json($page)
                ->header('Cache-Control', 'private, no-store');
        }

        $employees = $page['data'];
        $nextCursor = $page['next_cursor'];
        $hasMore = $page['has_more'];

        $stats = Cache::remember('monitoring.stats.v5', now()->addMinutes(5), function () {
            $master = DB::table('master_employees')
                ->selectRaw(
                    'COUNT(*) AS total, COALESCE(SUM(CASE WHEN ' .
                    self::SUDAH_SQL .
                    ' THEN 1 ELSE 0 END), 0) AS sudah'
                )
                ->first();

            $masterTotal = (int) ($master->total ?? 0);
            $sudah = (int) ($master->sudah ?? 0);
            $manualTotal = DB::table('manual_registrations')->count();

            return [
                'total' => $masterTotal + $manualTotal,
                'master_total' => $masterTotal,
                'manual_total' => $manualTotal,
                'sudah' => $sudah,
                'belum' => max(0, $masterTotal - $sudah),
                'percentage' => $masterTotal > 0
                    ? round($sudah * 100 / $masterTotal)
                    : 0,
            ];
        });

        $totalData = $stats['total'];
        $sudahDidata = $stats['sudah'];
        $belumDidata = $stats['belum'];
        $persentase = $stats['percentage'];
        $manualTotal = $stats['manual_total'];

        $institutions = Institution::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $kecamatans = collect();
        $kelurahans = collect();

        return view('monitoring.index', compact(
            'employees',
            'nextCursor',
            'hasMore',
            'institutions',
            'kecamatans',
            'kelurahans',
            'totalData',
            'sudahDidata',
            'belumDidata',
            'persentase',
            'manualTotal'
        ));
    }

    private function fetchPage(array $filters, NikSecurityService $nik): array
    {
        $source = $filters['source'] ?? 'all';
        $status = $filters['status'] ?? '';
        $phases = $this->resolvePhases($source, $status);

        if (!$phases) {
            return [
                'data' => [],
                'has_more' => false,
                'next_cursor' => null,
            ];
        }

        $phase = $filters['phase'] ?? $phases[0];

        if (!in_array($phase, $phases, true)) {
            $phase = $phases[0];
        }

        $phaseIndex = array_search($phase, $phases, true);
        $after = (int) ($filters['after'] ?? 0);

        for ($index = $phaseIndex; $index < count($phases); $index++) {
            $currentPhase = $phases[$index];

            if ($currentPhase === 'manual') {
                $query = DB::table('manual_registrations')
                    ->where('id', '>', $after);

                $this->applyManualFilters($query, $filters);

                $rows = $query->orderBy('id')
                    ->limit(self::PAGE_SIZE + 1)
                    ->get([
                        'id',
                        'nik_encrypted',
                        'nip',
                        'institution',
                        'name',
                        'province_name',
                        'regency_name',
                        'district_name',
                        'village_name',
                        'rt',
                        'rw',
                        'created_at',
                    ]);

                $map = fn ($row) => $this->mapManual($row, $nik);
            } else {
                $query = DB::table('master_employees')
                    ->where('id', '>', $after);

                $query->whereRaw(
                    $currentPhase === 'sudah'
                        ? self::SUDAH_SQL
                        : 'NOT ' . self::SUDAH_SQL
                );

                $this->applyMasterFilters($query, $filters);

                $rows = $query->orderBy('id')
                    ->limit(self::PAGE_SIZE + 1)
                    ->get([
                        'id',
                        'nik_encrypted',
                        'nama',
                        'name',
                        'nama_kepala_keluarga',
                        'no_kk',
                        'kecamatan',
                        'kelurahan',
                        'rt_rw',
                        'idsubsls',
                        'alamat',
                        'status_didata',
                        'respSE26_keberadaan_klrg',
                        'status',
                        'assignment_id',
                        'code_identity',
                        'assignment_status_alias',
                        'institution_id',
                        'instansi',
                        'profesi',
                        'status_kerja_label',
                        'profesi_lainnya',
                        'respSE26_assignment_id',
                        'respSE26_no_kk',
                        'respSE26_nama',
                        'respSE26_code_identity',
                    ]);

                $map = fn ($row) => $this->mapMaster($row, $nik);
            }

            $moreInPhase = $rows->count() > self::PAGE_SIZE;
            $currentRows = $rows->take(self::PAGE_SIZE);

            if ($currentRows->isEmpty()) {
                $after = 0;
                continue;
            }

            $data = $currentRows->map($map)->values()->all();
            $lastId = (int) $currentRows->last()->id;

            if ($moreInPhase) {
                return [
                    'data' => $data,
                    'has_more' => true,
                    'next_cursor' => [
                        'phase' => $currentPhase,
                        'after' => $lastId,
                    ],
                ];
            }

            if (isset($phases[$index + 1])) {
                return [
                    'data' => $data,
                    'has_more' => true,
                    'next_cursor' => [
                        'phase' => $phases[$index + 1],
                        'after' => 0,
                    ],
                ];
            }

            return [
                'data' => $data,
                'has_more' => false,
                'next_cursor' => null,
            ];
        }

        return [
            'data' => [],
            'has_more' => false,
            'next_cursor' => null,
        ];
    }

    private function resolvePhases(string $source, string $status): array
    {
        if ($source === 'manual') {
            return in_array(
                $status,
                ['', 'Perlu Tindak Lanjut'],
                true
            ) ? ['manual'] : [];
        }

        if ($status === 'Perlu Tindak Lanjut') {
            return $source === 'master' ? [] : ['manual'];
        }

        if ($status === 'Sudah Didata') {
            return ['sudah'];
        }

        if ($status === 'Belum Didata') {
            return ['belum'];
        }

        return $source === 'master'
            ? ['belum', 'sudah']
            : ['belum', 'sudah', 'manual'];
    }

    private function applyMasterFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $this->likeValue($filters['search']);

            $query->where(function ($builder) use ($search) {
                foreach ([
                    'nama',
                    'name',
                    'nama_kepala_keluarga',
                    'nip',
                    'assignment_id',
                    'idsubsls',
                    'rt_rw',
                    'kecamatan',
                    'kelurahan',
                    'instansi',
                    'status_didata',
                ] as $column) {
                    $builder->orWhere($column, 'like', $search);
                }
            });
        }

        if (!empty($filters['institution'])) {
            $name = Institution::query()
                ->whereKey($filters['institution'])
                ->value('name');

            if ($name === null) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($builder) use ($filters, $name) {
                    $builder
                        ->where('institution_id', $filters['institution'])
                        ->orWhere('instansi', $name);
                });
            }
        }

        if (!empty($filters['kecamatan'])) {
            $query->where('kecamatan', $filters['kecamatan']);
        }

        if (!empty($filters['kelurahan'])) {
            $query->where('kelurahan', $filters['kelurahan']);
        }
    }

    private function applyManualFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $this->likeValue($filters['search']);

            $query->where(function ($builder) use ($search) {
                foreach ([
                    'name',
                    'nip',
                    'institution',
                    'province_name',
                    'regency_name',
                    'district_name',
                    'village_name',
                    'rt',
                    'rw',
                ] as $column) {
                    $builder->orWhere($column, 'like', $search);
                }
            });
        }

        if (!empty($filters['institution'])) {
            $name = Institution::query()
                ->whereKey($filters['institution'])
                ->value('name');

            if ($name !== null) {
                $query->where('institution', $name);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($filters['kecamatan'])) {
            $query->where(
                'district_name',
                $filters['kecamatan']
            );
        }

        if (!empty($filters['kelurahan'])) {
            $query->where(
                'village_name',
                $filters['kelurahan']
            );
        }
    }

    private function mapMaster(object $row, NikSecurityService $nik): array
    {
        $sudah = $this->isSudah($row);

        return [
            'key' => 'master:' . $row->id,
            'id' => $row->id,
            'source' => 'master',
            'nama' => $row->nama ?: $row->name,
            'nama_kepala_keluarga' => $row->nama_kepala_keluarga,
            'nik_masked' => $this->maskNik($row->nik_encrypted, $nik),
            'no_kk' => $this->maskNumber($row->no_kk),
            'kecamatan' => $row->kecamatan,
            'kelurahan' => $row->kelurahan,
            'rt_rw' => $row->rt_rw,
            'idsubsls' => $row->idsubsls,
            'alamat' => $row->alamat,
            'sudah_didata' => $sudah
                ? 'Sudah Didata'
                : 'Belum Didata',
            'status' => $row->status,
            'assignment_id' => $row->assignment_id,
            'code_identity' => $row->code_identity,
            'assignment_status_alias' => $row->assignment_status_alias,
            'institution_id' => $row->institution_id,
            'instansi' => $row->instansi,
            'profesi' => $row->profesi,
            'status_kerja_label' => $row->status_kerja_label,
            'profesi_lainnya' => $row->profesi_lainnya,
            'respSE26_assignment_id' => $row->respSE26_assignment_id,
            'respSE26_no_kk' => $this->maskNumber($row->respSE26_no_kk),
            'respSE26_nama' => $row->respSE26_nama,
            'respSE26_code_identity' => $row->respSE26_code_identity,
            'respSE26_keberadaan_klrg' => $row->respSE26_keberadaan_klrg,
        ];
    }

    private function mapManual(object $row, NikSecurityService $nik): array
    {
        return [
            'key' => 'manual:' . $row->id,
            'id' => $row->id,
            'source' => 'manual',
            'nama' => $row->name,
            'nama_kepala_keluarga' => null,
            'nik_masked' => $this->maskNik($row->nik_encrypted, $nik),
            'no_kk' => null,
            'kecamatan' => $row->district_name,
            'kelurahan' => $row->village_name,
            'rt_rw' => ($row->rt ?? '-') . '/' . ($row->rw ?? '-'),
            'idsubsls' => null,
            'alamat' => implode(', ', array_filter([
                $row->village_name,
                $row->district_name,
                $row->regency_name,
                $row->province_name,
            ])),
            'sudah_didata' => 'Perlu Tindak Lanjut',
            'status' => 'Pendaftaran Manual',
            'assignment_id' => null,
            'code_identity' => null,
            'assignment_status_alias' => null,
            'institution_id' => null,
            'instansi' => $row->institution,
            'profesi' => null,
            'status_kerja_label' => null,
            'profesi_lainnya' => null,
            'respSE26_assignment_id' => null,
            'respSE26_no_kk' => null,
            'respSE26_nama' => null,
            'respSE26_code_identity' => null,
            'respSE26_keberadaan_klrg' => null,
        ];
    }

    private function isSudah(object $row): bool
    {
        $status = mb_strtolower(
            trim((string) $row->status_didata)
        );

        $presence = mb_strtolower(
            trim((string) $row->respSE26_keberadaan_klrg)
        );

        $presence = preg_replace(
            '/^[0-9]+\.\s*/u',
            '',
            $presence
        );

        return in_array($status, [
            'sudah didata',
            'sudah',
            'ditemukan',
            'baru',
            '1',
        ], true) || in_array(
            $presence,
            ['ditemukan', 'baru'],
            true
        );
    }

    private function maskNik(?string $encrypted, NikSecurityService $nik): string
    {
        if (!$encrypted) {
            return '-';
        }

        try {
            $value = $nik->decrypt($encrypted);

            if (!preg_match('/^[0-9]{16}$/D', $value)) {
                return '-';
            }

            return substr($value, 0, 2) .
                str_repeat('*', 12) .
                substr($value, -2);
        } catch (Throwable) {
            return '-';
        }
    }

    private function maskNumber(?string $number): string
    {
        $number = preg_replace('/\D/', '', (string) $number);

        if (strlen($number) < 6) {
            return '-';
        }

        return substr($number, 0, 2) .
            str_repeat('*', strlen($number) - 4) .
            substr($number, -2);
    }

    private function likeValue(string $value): string
    {
        return '%' . addcslashes(trim($value), '%_\\') . '%';
    }

    private function authorizeExport(Request $request): ?Response
    {
        abort_unless(
            config('monitoring.export_enabled', false),
            403
        );

        if (
            app()->environment('production') &&
            !$request->secure()
        ) {
            abort(403, 'Ekspor hanya dapat dilakukan melalui HTTPS.');
        }

        $username = (string) config('monitoring.export_username', '');
        $hash = (string) config('monitoring.export_password_hash', '');

        abort_if(
            $username === '' || $hash === '',
            503,
            'Autentikasi ekspor belum dikonfigurasi.'
        );

        $valid = hash_equals(
            $username,
            (string) $request->getUser()
        ) && password_verify(
            (string) $request->getPassword(),
            $hash
        );

        if (!$valid) {
            return response(
                'Autentikasi petugas diperlukan untuk mengunduh data.',
                401
            )
                ->header(
                    'WWW-Authenticate',
                    'Basic realm="Ekspor Monitoring"'
                )
                ->header('Cache-Control', 'private, no-store');
        }

        return null;
    }

    public function exportAll(Request $request)
    {
        if ($response = $this->authorizeExport($request)) {
            return $response;
        }

        return (new MonitoringExport())->download(
            'monitoring_semua_data_' .
            now()->format('Ymd_His') .
            '.xlsx'
        );
    }

    public function exportFiltered(Request $request)
    {
        if ($response = $this->authorizeExport($request)) {
            return $response;
        }

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'in:all,master,manual'],
            'institution' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:Sudah Didata,Belum Didata,Perlu Tindak Lanjut'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
        ]);

        return (new MonitoringExport($filters))->download(
            'monitoring_sesuai_filter_' .
            now()->format('Ymd_His') .
            '.xlsx'
        );
    }
}
