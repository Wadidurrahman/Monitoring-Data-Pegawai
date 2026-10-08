<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use InvalidArgumentException;
use RuntimeException;

class NikSecurityService
{

    public function normalize(string $nik): string
    {
        return preg_replace('/\D/', '', trim($nik));
    }

    public function validate(string $nik): string
    {
        $nik = $this->normalize($nik);

        if (strlen($nik) !== 16) {
            throw new InvalidArgumentException(
                'NIK harus terdiri dari 16 digit.'
            );
        }

        return $nik;
    }

    public function encrypt(string $nik): string
    {
        $nik = $this->validate($nik);

        return Crypt::encryptString($nik);
    }

    public function decrypt(string $encryptedNik): string
    {
        return Crypt::decryptString($encryptedNik);
    }

    public function lookup(string $nik): string
    {
        $nik = $this->validate($nik);

        $key = config('security.nik_lookup_key');

        if (empty($key)) {
            throw new RuntimeException(
                'NIK_LOOKUP_KEY belum dikonfigurasi.'
            );
        }

        return hash_hmac(
            'sha256',
            $nik,
            $key
        );
    }

    public function mask(string $nik): string
    {
        $nik = $this->validate($nik);

        return substr($nik, 0, 4)
            . str_repeat('*', 8)
            . substr($nik, -4);
    }

    public function maskEncrypted(string $encryptedNik): string
    {
        return $this->mask(
            $this->decrypt($encryptedNik)
        );
    }
}