<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualRegistrationRequest;
use App\Models\ManualRegistration;
use App\Models\MasterEmployee;
use App\Services\NikSecurityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ManualRegistrationController extends Controller
{
    public function __construct(private readonly NikSecurityService $nikSecurity)
    {
    }

    public function store(StoreManualRegistrationRequest $request): JsonResponse
    {
        try {
            $payload = json_decode(Crypt::decryptString($request->validated('registration_token')), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Sesi pengecekan NIK tidak valid. Silakan lakukan pengecekan ulang.'], 422);
        }

        if (empty($payload['nik']) || empty($payload['institution']) || empty($payload['expires_at'])) {
            return response()->json(['message' => 'Data pengecekan tidak valid. Silakan cek NIK kembali.'], 422);
        }

        if (now()->timestamp > (int) $payload['expires_at']) {
            return response()->json(['message' => 'Sesi pengecekan telah kedaluwarsa. Silakan cek NIK kembali.'], 422);
        }

        $nip = $payload['nip'] ?? null;

        if (!is_string($nip) || preg_match('/^[0-9]{18}$/D', $nip) !== 1) {
            return response()->json(['message' => 'NIP pada sesi pengecekan tidak valid. Silakan cek kembali.'], 422);
        }

        if (!hash_equals($nip, (string) $request->validated('nip'))) {
            return response()->json(['message' => 'NIP tidak sesuai dengan data pengecekan sebelumnya.'], 422);
        }

        $nik = $payload['nik'];
        $institution = trim($payload['institution']);
        $nikLookup = $this->nikSecurity->lookup($nik);

        if (MasterEmployee::query()->where('nik_lookup', $nikLookup)->exists()) {
            return response()->json(['message' => 'NIK telah tersedia pada data master. Silakan lakukan pengecekan ulang.'], 409);
        }

        if (ManualRegistration::query()->where('nik_lookup', $nikLookup)->exists()) {
            return response()->json(['message' => 'Data dengan NIK tersebut sudah pernah dikirim.'], 409);
        }

        $registration = DB::transaction(function () use ($request, $nik, $nip, $nikLookup, $institution) {
            return ManualRegistration::create([
                'nik_encrypted' => $this->nikSecurity->encrypt($nik),
                'nik_lookup' => $nikLookup,
                'nip' => $nip,
                'institution' => $institution,
                'name' => $request->validated('name'),
                'email' => strtolower(trim($request->validated('email'))),
'email_consent_at' => $request->boolean('email_consent') ? now() : null,
                'province_name' => $request->validated('province_name'),
                'regency_name' => $request->validated('regency_name'),
                'district_name' => $request->validated('district_name'),
                'village_name' => $request->validated('village_name'),
                'rt' => $request->validated('rt'),
                'rw' => $request->validated('rw'),
            ]);
        });

        return response()->json([
            'result' => 'success',
            'message' => 'Data berhasil disimpan.',
            'data' => [
                'id' => $registration->id,
                'name' => $registration->name,
                'masked_nik' => $this->nikSecurity->mask($nik),
                'institution' => $registration->institution,
            ],
        ], 201);
    }
}
