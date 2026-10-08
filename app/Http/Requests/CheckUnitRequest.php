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
            'institution' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.regex' => 'NIK harus terdiri dari 16 digit angka.',
            'institution.required' => 'Instansi wajib dipilih.',
            'institution.max' => 'Nama instansi maksimal 255 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nik' => preg_replace('/\D/', '', (string) $this->nik),
            'institution' => preg_replace('/\s+/', ' ', trim((string) $this->institution)),
        ]);
    }
}
