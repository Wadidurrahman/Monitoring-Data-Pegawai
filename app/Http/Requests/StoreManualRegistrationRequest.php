<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_token' => [
                'required',
                'string',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'province_name' => [
                'required',
                'string',
                'max:255',
            ],

            'regency_name' => [
                'required',
                'string',
                'max:255',
            ],

            'district_name' => [
                'required',
                'string',
                'max:255',
            ],

            'village_name' => [
                'required',
                'string',
                'max:255',
            ],

            'rw' => [
                'nullable',
                'string',
                'regex:/^\d{3}$/',
            ],

            'rt' => [
                'nullable',
                'string',
                'regex:/^\d{3}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'registration_token.required' =>
                'Sesi pengecekan NIK tidak ditemukan.',

            'name.required' =>
                'Nama wajib diisi.',

            'province_name.required' =>
                'Provinsi wajib diisi.',

            'regency_name.required' =>
                'Kabupaten/Kota wajib diisi.',

            'district_name.required' =>
                'Kecamatan wajib diisi.',

            'village_name.required' =>
                'Kelurahan/Desa wajib diisi.',

            'rw.regex' =>
                'RW hanya boleh berisi maksimal 3 digit angka.',

            'rt.regex' =>
                'RT hanya boleh berisi maksimal 3 digit angka.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim(
                (string) $this->name
            ),

            'province_name' => trim(
                (string) $this->province_name
            ),

            'regency_name' => trim(
                (string) $this->regency_name
            ),

            'district_name' => trim(
                (string) $this->district_name
            ),

            'village_name' => trim(
                (string) $this->village_name
            ),

            'rw' => $this->normalizeRtRw(
                $this->rw
            ),

            'rt' => $this->normalizeRtRw(
                $this->rt
            ),
        ]);
    }

    private function normalizeRtRw(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (
            preg_match(
                '/^\d{1,3}$/',
                $value
            ) !== 1
        ) {
            return $value;
        }

        return str_pad(
            $value,
            3,
            '0',
            STR_PAD_LEFT
        );
    }
}
