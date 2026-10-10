<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckUnitRequest;
use App\Models\Institution;
use App\Models\ManualRegistration;
use App\Models\MasterEmployee;
use App\Services\NikSecurityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CheckUnitController extends Controller
{
    public function __construct(
        private readonly NikSecurityService $nikSecurity
    ) {
    }

    public function check(CheckUnitRequest $request): JsonResponse
    {
        $nik = (string) $request->validated('nik');
        $nip = (string) $request->validated('nip');

        $institution = trim(preg_replace(
            '/\s+/u',
            ' ',
            (string) $request->validated('institution')
        ));

        if (preg_match('/^[0-9]{16}$/D', $nik) !== 1 ||
            preg_match('/^[0-9]{18}$/D', $nip) !== 1 ||
            $institution === '') {
            return response()->json([
                'message' => 'Data pengecekan tidak valid.',
            ], 422);
        }

        $nikLookup = $this->nikSecurity->lookup($nik);

        $knownInstitution = Institution::query()
            ->where('is_active', true)
            ->whereRaw(
                'LOWER(TRIM(name)) = ?',
                [mb_strtolower($institution)]
            )
            ->first(['id', 'name']);

        $institutionName = $knownInstitution?->name ?? $institution;
        $institutionId = $knownInstitution?->id;

        $result = DB::transaction(function () use (
            $nikLookup,
            $nip,
            $institutionName,
            $institutionId
        ) {
            $employee = MasterEmployee::query()
                ->where('nik_lookup', $nikLookup)
                ->lockForUpdate()
                ->first();

            if (!$employee) {
                return [
                    'employee' => null,
                    'conflict' => false,
                ];
            }

            $storedNip = trim((string) $employee->nip);
            $storedInstitution = trim((string) $employee->instansi);

            if ($storedNip !== '' && !hash_equals($storedNip, $nip)) {
                return [
                    'employee' => null,
                    'conflict' => true,
                ];
            }

            if (
                $storedInstitution !== '' &&
                mb_strtolower($storedInstitution) !== mb_strtolower($institutionName)
            ) {
                return [
                    'employee' => null,
                    'conflict' => true,
                ];
            }

            if (
                $institutionId !== null &&
                $employee->institution_id !== null &&
                (string) $employee->institution_id !== (string) $institutionId
            ) {
                return [
                    'employee' => null,
                    'conflict' => true,
                ];
            }

            $updates = [];

            if ($storedNip === '') {
                $updates['nip'] = $nip;
            }

            if ($storedInstitution === '') {
                $updates['instansi'] = $institutionName;
            }

            if (
                $institutionId !== null &&
                $employee->institution_id === null
            ) {
                $updates['institution_id'] = $institutionId;
            }

            if ($updates !== []) {
                $employee->forceFill($updates)->save();
            }

            return [
                'employee' => $employee,
                'conflict' => false,
            ];
        }, 3);

        if ($result['conflict']) {
            return response()->json([
                'message' => 'Data NIP atau instansi memerlukan verifikasi petugas. Data yang telah tersimpan tidak diubah.',
            ], 409);
        }

        $employee = $result['employee'];

        if ($employee) {
            $name = trim((string) (
                $employee->name
                ?: $employee->nama
                ?: $employee->respSE26_nama
                ?: $employee->nama_kepala_keluarga
                ?: ''
            ));

            $status = $this->resolveStatus($employee);

            return response()->json([
                'result' => 'found',
                'message' => $status === 'Sudah Didata'
                    ? 'Data Anda tercatat sudah didata.'
                    : 'Data Anda tercatat belum didata dan memerlukan tindak lanjut.',
                'data' => [
                    'masked_nik' => $this->nikSecurity->mask($nik),
                    'name' => $name,
                    'institution' => $employee->instansi,
                    'status' => $status,
                ],
            ]);
        }

        $manualRegistration = ManualRegistration::query()
            ->where('nik_lookup', $nikLookup)
            ->first();

        if ($manualRegistration) {
            return response()->json([
                'result' => 'manual_found',
                'message' => 'Data Anda sebelumnya sudah pernah dikirim.',
                'data' => [
                    'masked_nik' => $this->nikSecurity->mask($nik),
                    'institution' => $manualRegistration->institution,
                ],
            ]);
        }

        $registrationToken = Crypt::encryptString(json_encode([
            'nik' => $nik,
            'nip' => $nip,
            'institution' => $institutionName,
            'expires_at' => now()->addMinutes(15)->timestamp,
        ], JSON_THROW_ON_ERROR));

        return response()->json([
            'result' => 'not_found',
            'message' => 'NIK tidak ditemukan pada data master. Silakan lengkapi data Anda.',
            'data' => [
                'masked_nik' => $this->nikSecurity->mask($nik),
                'institution' => $institutionName,
                'registration_token' => $registrationToken,
            ],
        ]);
    }

    private function resolveStatus(MasterEmployee $employee): string
    {
        $status = mb_strtolower(trim((string) $employee->status_didata));

        $presence = mb_strtolower(trim(
            (string) $employee->respSE26_keberadaan_klrg
        ));

        $presence = preg_replace('/^[0-9]+\.\s*/u', '', $presence);

        $sudah = in_array($status, [
            'sudah didata',
            'sudah',
            'ditemukan',
            'baru',
            '1',
        ], true) || in_array($presence, [
            'ditemukan',
            'baru',
        ], true);

        return $sudah ? 'Sudah Didata' : 'Belum Didata';
    }
}
