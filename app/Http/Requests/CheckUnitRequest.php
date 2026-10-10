<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'regex:/^\d{16}$/'],
            'nip' => ['required', 'string', 'regex:/^[0-9]{18}$/D'],
            'institution' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.regex' => 'NIK harus terdiri dari 16 digit angka.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.regex' => 'NIP harus terdiri dari 18 digit angka.',
            'institution.required' => 'Instansi wajib dipilih.',
            'institution.max' => 'Nama instansi maksimal 255 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nik' => preg_replace('/\D/', '', (string) $this->nik),
            'nip' => is_string($this->input('nip'))? trim($this->input('nip')): $this->input('nip'),
            'institution' => preg_replace('/\s+/', ' ', trim((string) $this->institution)),
        ]);
    }
}
