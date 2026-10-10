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
            'registration_token' => ['required', 'string'],
            'nip' => ['required', 'string', 'regex:/^[0-9]{18}$/D'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:254', 'ends_with:@gmail.com'],
            'email_consent' => ['sometimes', 'boolean'],
            'province_name' => ['required', 'string', 'max:255'],
            'regency_name' => ['required', 'string', 'max:255'],
            'district_name' => ['required', 'string', 'max:255'],
            'village_name' => ['required', 'string', 'max:255'],
            'rw' => ['nullable', 'string', 'regex:/^\d{3}$/'],
            'rt' => ['nullable', 'string', 'regex:/^\d{3}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'registration_token.required' => 'Sesi pengecekan NIK tidak ditemukan.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.regex' => 'NIP harus terdiri dari 18 digit angka.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Alamat Gmail wajib diisi.',
            'email.email' => 'Format alamat Gmail tidak valid.',
            'email.ends_with' => 'Gunakan alamat email dengan domain @gmail.com.',
            'email.max' => 'Alamat Gmail terlalu panjang.',
            'province_name.required' => 'Provinsi wajib diisi.',
            'regency_name.required' => 'Kabupaten/Kota wajib diisi.',
            'district_name.required' => 'Kecamatan wajib diisi.',
            'village_name.required' => 'Kelurahan/Desa wajib diisi.',
            'rw.regex' => 'RW harus terdiri dari 3 digit angka.',
            'rt.regex' => 'RT harus terdiri dari 3 digit angka.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'nip' => is_string($this->input('nip')) ? trim($this->input('nip')) : $this->input('nip'),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'province_name' => trim((string) $this->input('province_name')),
            'regency_name' => trim((string) $this->input('regency_name')),
            'district_name' => trim((string) $this->input('district_name')),
            'village_name' => trim((string) $this->input('village_name')),
            'rw' => $this->normalizeRtRw($this->input('rw')),
            'rt' => $this->normalizeRtRw($this->input('rt')),
        ]);
    }

    private function normalizeRtRw(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{1,3}$/', $value) !== 1) {
            return $value;
        }

        return str_pad($value, 3, '0', STR_PAD_LEFT);
    }
}
