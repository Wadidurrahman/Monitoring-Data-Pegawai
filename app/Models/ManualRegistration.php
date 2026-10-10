<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik_encrypted',
        'nik_lookup',
        'nip',
        'institution',
        'name',
        'email',
        'email_consent_at',
        'province_name',
        'regency_name',
        'district_name',
        'village_name',
        'rt',
        'rw',
    ];

    protected $hidden = [
        'nik_encrypted',
        'nik_lookup',
        'email',
    ];

    protected $casts = [
        'email_consent_at' => 'datetime',
    ];
}
