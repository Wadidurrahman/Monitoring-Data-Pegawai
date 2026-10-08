<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckUnitRequest;
use App\Models\ManualRegistration;
use App\Models\MasterEmployee;
use App\Services\NikSecurityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;

class CheckUnitController extends Controller
{
    public function __construct(
        private readonly NikSecurityService $nikSecurity
    ) {}

    public function check(CheckUnitRequest $request): JsonResponse
    {
        $nik = $request->validated('nik');

        $institution = preg_replace(
            '/\s+/',
            ' ',
            trim($request->validated('institution'))
        );

        $nikLookup = $this->nikSecurity->lookup($nik);

        $employees = MasterEmployee::query()
            ->where('nik_lookup', $nikLookup)
            ->get();

        if ($employees->isEmpty()) {
            $manualRegistration = ManualRegistration::query()
                ->where('nik_lookup', $nikLookup)
                ->first();

            if ($manualRegistration) {
                $manualRegistration->update([
                    'institution' => $institution,
                ]);

                return response()->json([
                    'result' => 'manual_found',
                    'message' => 'Data Anda sebelumnya sudah pernah dikirim.',
                    'data' => [
                        'masked_nik' => $this->nikSecurity->maskEncrypted(
                            $manualRegistration->nik_encrypted
                        ),
                        'institution' => $institution,
                    ],
                ]);
            }

            $registrationToken = Crypt::encryptString(
                json_encode([
                    'nik' => $nik,
                    'institution' => $institution,
                    'expires_at' => now()->addMinutes(15)->timestamp,
                ], JSON_THROW_ON_ERROR)
            );

            return response()->json([
                'result' => 'not_found',
                'message' => 'NIK tidak ditemukan pada data master.',
                'data' => [
                    'masked_nik' => $this->nikSecurity->mask($nik),
                    'institution' => $institution,
                    'registration_token' => $registrationToken,
                ],
            ]);
        }

        MasterEmployee::query()
            ->where('nik_lookup', $nikLookup)
            ->update([
                'instansi' => $institution,
            ]);

        $name = $employees
    ->map(function (MasterEmployee $employee) {
        return trim((string) (
            $employee->name
            ?: $employee->nama
            ?: $employee->respSE26_nama
            ?: $employee->nama_kepala_keluarga
            ?: ''
        ));
    })
    ->filter()
    ->first();

        return response()->json([
            'result' => 'found',
            'message' => 'Data Anda sudah terdata.',
            'data' => [
                'masked_nik' => $this->nikSecurity->mask($nik),
                'name' => $name,
                'institution' => $institution,
                'status' => 'Sudah Didata',
            ],
        ]);
    }
}
