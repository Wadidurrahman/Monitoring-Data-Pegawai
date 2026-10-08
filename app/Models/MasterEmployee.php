<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterEmployee extends Model
{
    protected $fillable = [
        'assignment_id',
        'level_6_id',
        'level_2_name',
        'level_3_name',
        'level_4_name',
        'level_6_name',
        'nik_encrypted',
        'nik_lookup',
        'name',
        'status',
        'instansi',
    ];

    protected $hidden = [
        'nik_encrypted',
        'nik_lookup',
    ];

    public function getTrackingStatusAttribute(): string
    {
        $status = mb_strtolower(trim((string) $this->status));
        $status = preg_replace('/^\d+\s*\.\s*/u', '', $status);

        return in_array($status, ['ditemukan', 'baru'], true)
            ? 'Sudah Didata'
            : 'Belum Didata';
    }
}
