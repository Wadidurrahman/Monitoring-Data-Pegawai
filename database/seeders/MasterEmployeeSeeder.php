<?php

namespace Database\Seeders;

use App\Models\MasterEmployee;
use App\Services\NikSecurityService;
use Illuminate\Database\Seeder;

class MasterEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $nikSecurity = app(NikSecurityService::class);

        $data = [
            [
                'nik' => '3574010101010001',
                'assignment_id' => 'ASSIGN-001',
                'level_6_id' => 'L6-001',
                'level_2_name' => 'Jawa Timur',
                'level_3_name' => 'Kota Probolinggo',
                'level_4_name' => 'Kademangan',
                'level_6_name' => 'Pilanggede',
                'name' => 'Pegawai Dummy Satu',
                'status' => 'Ditemukan',
                'institution' => 'BPS Kota Probolinggo',
            ],

            [
                'nik' => '3574010101010002',
                'assignment_id' => 'ASSIGN-002',
                'level_6_id' => 'L6-002',
                'level_2_name' => 'Jawa Timur',
                'level_3_name' => 'Kota Probolinggo',
                'level_4_name' => 'Kanigaran',
                'level_6_name' => 'Kanigaran',
                'name' => 'Pegawai Dummy Dua',
                'status' => 'Baru',
                'institution' => 'BPS Kota Probolinggo',
            ],

            [
                'nik' => '3574010101010003',
                'assignment_id' => 'ASSIGN-003',
                'level_6_id' => 'L6-003',
                'level_2_name' => 'Jawa Timur',
                'level_3_name' => 'Kota Probolinggo',
                'level_4_name' => 'Mayangan',
                'level_6_name' => 'Mangunharjo',
                'name' => 'Pegawai Dummy Tiga',
                'status' => 'Belum Lengkap',
                'institution' => 'BPS Kota Probolinggo',
            ],
        ];

        foreach ($data as $item) {
            $nik = $item['nik'];

            MasterEmployee::updateOrCreate(
                [
                    'nik_lookup' => $nikSecurity->lookup($nik),
                ],
                [
                    'assignment_id' => $item['assignment_id'],
                    'level_6_id' => $item['level_6_id'],
                    'level_2_name' => $item['level_2_name'],
                    'level_3_name' => $item['level_3_name'],
                    'level_4_name' => $item['level_4_name'],
                    'level_6_name' => $item['level_6_name'],


                    'nik_encrypted' => $nikSecurity->encrypt($nik),

                    'name' => $item['name'],
                    'status' => $item['status'],
                    'institution' => $item['institution'],
                ]
            );
        }
    }
}
