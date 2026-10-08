<?php

namespace App\Services;

class InstitutionService
{
    public function normalize(?string $institution): string
    {
        $institution = trim((string) $institution);
        $institution = preg_replace('/\s+/', ' ', $institution);

        return mb_strtolower($institution);
    }

    public function matches(?string $first, ?string $second): bool
    {
        return $this->normalize($first) === $this->normalize($second);
    }
}
