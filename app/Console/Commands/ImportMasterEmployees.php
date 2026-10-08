<?php

namespace App\Console\Commands;

use App\Services\NikSecurityService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class ImportMasterEmployees extends Command
{
    protected $signature = 'master:import-staging {--execute} {--skip-problematic} {--chunk=250}';

    protected $description = 'Import data master valid dan unik dari staging terbaru';

    public function handle(NikSecurityService $nikSecurity): int
    {
        set_time_limit(0);

        $source = 'staging_master_employees_import_20261008';
        $target = 'master_employees';
        $execute = (bool) $this->option('execute');
        $skipProblematic = (bool) $this->option('skip-problematic');
        $chunkSize = filter_var(
            $this->option('chunk'),
            FILTER_VALIDATE_INT
        );

        $fields = [
            'idsubsls',
            'rt_rw',
            'kecamatan',
            'kelurahan',
            'nama_kepala_keluarga',
            'nama',
            'keberadaan_dtsen_label',
            'profesi',
            'profesi_lainnya',
            'status_kerja_label',
            'code_identity',
            'assignment_status_alias',
            'assignment_id',
            'nama_principal',
            'alamat',
            'no_kk',
            'respSE26_assignment_id',
            'respSE26_no_kk',
            'respSE26_nama',
            'respSE26_code_identity',
            'respSE26_keberadaan_klrg',
            '_file_sumber',
            '_baris_sumber',
        ];

        try {
            if ($chunkSize === false || $chunkSize < 1 || $chunkSize > 1000) {
                throw new RuntimeException(
                    'Ukuran chunk harus antara 1 sampai 1000.'
                );
            }

            if (!Schema::hasTable($source) || !Schema::hasTable($target)) {
                throw new RuntimeException(
                    'Tabel staging atau master tidak ditemukan.'
                );
            }

            $requiredSource = array_merge(
                ['id', 'nik', 'status_didata'],
                $fields
            );

            $requiredTarget = array_merge(
                [
                    'nik_encrypted',
                    'nik_lookup',
                    'name',
                    'family_card_number',
                    'status',
                    'status_didata',
                    'institution_id',
                    'instansi',
                    'created_at',
                    'updated_at',
                ],
                $fields
            );

            $sourceColumns = Schema::getColumnListing($source);
            $targetColumns = Schema::getColumnListing($target);

            $missingSource = array_diff($requiredSource, $sourceColumns);
            $missingTarget = array_diff($requiredTarget, $targetColumns);

            if ($missingSource !== []) {
                throw new RuntimeException(
                    'Kolom staging tidak ditemukan: ' .
                    implode(', ', $missingSource)
                );
            }

            if ($missingTarget !== []) {
                throw new RuntimeException(
                    'Kolom master tidak ditemukan: ' .
                    implode(', ', $missingTarget)
                );
            }

            $totalStaging = DB::table($source)->count();
            $totalMaster = DB::table($target)->count();

            if ($totalStaging === 0) {
                throw new RuntimeException('Staging kosong.');
            }

            if ($totalMaster !== 0) {
                throw new RuntimeException(
                    'Master harus kosong sebelum import penggantian penuh.'
                );
            }

            $invalidNik = DB::table($source)
                ->where(function ($query) {
                    $query
                        ->whereNull('nik')
                        ->orWhereRaw(
                            "TRIM(nik) NOT REGEXP '^[0-9]{16}$'"
                        );
                })
                ->count();

            $emptyStatus = DB::table($source)
                ->where(function ($query) {
                    $query
                        ->whereNull('status_didata')
                        ->orWhereRaw("TRIM(status_didata) = ''");
                })
                ->count();

            $duplicates = DB::table($source)
                ->selectRaw(
                    'TRIM(nik) AS nik_bersih, COUNT(*) AS jumlah'
                )
                ->whereRaw(
                    "TRIM(nik) REGEXP '^[0-9]{16}$'"
                )
                ->groupByRaw('TRIM(nik)')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            $duplicateSet = [];
            $duplicateRows = 0;

            foreach ($duplicates as $duplicate) {
                $duplicateSet[
                    'n:' . $duplicate->nik_bersih
                ] = true;

                $duplicateRows += (int) $duplicate->jumlah;
            }

            $eligible = $totalStaging - $invalidNik - $duplicateRows;

            $this->newLine();

            $this->table(
                ['Pemeriksaan', 'Hasil'],
                [
                    ['Total staging', number_format($totalStaging, 0, ',', '.')],
                    ['Total master', number_format($totalMaster, 0, ',', '.')],
                    ['NIK tidak valid', number_format($invalidNik, 0, ',', '.')],
                    ['Kelompok duplikat', number_format($duplicates->count(), 0, ',', '.')],
                    ['Baris duplikat', number_format($duplicateRows, 0, ',', '.')],
                    ['Status kosong', number_format($emptyStatus, 0, ',', '.')],
                    ['Kandidat import', number_format($eligible, 0, ',', '.')],
                ]
            );

            if ($emptyStatus > 0) {
                throw new RuntimeException(
                    'Status pendataan kosong ditemukan.'
                );
            }

            if ($eligible <= 0) {
                throw new RuntimeException(
                    'Tidak ada kandidat data yang dapat diimport.'
                );
            }

            $sampleNik = '0000000000000000';

            $encrypted = $nikSecurity->encrypt($sampleNik);

            if ($nikSecurity->decrypt($encrypted) !== $sampleNik) {
                throw new RuntimeException(
                    'Pemeriksaan enkripsi NIK gagal.'
                );
            }

            $nikSecurity->lookup($sampleNik);

            $indexes = collect(
                DB::select("SHOW INDEX FROM `{$target}`")
            )->groupBy('Key_name');

            $hasUniqueLookup = $indexes->contains(
                function ($parts) {
                    if ($parts->count() !== 1) {
                        return false;
                    }

                    $index = $parts->first();

                    return (int) $index->Non_unique === 0
                        && $index->Column_name === 'nik_lookup';
                }
            );

            if (!$execute) {
                $this->newLine();
                $this->info('Validasi awal selesai.');
                $this->line(
                    'Unique index NIK: ' .
                    ($hasUniqueLookup ? 'Tersedia' : 'Belum tersedia')
                );
                $this->warn(
                    'Tidak ada data yang diubah.'
                );

                return self::SUCCESS;
            }

            if (!$hasUniqueLookup) {
                throw new RuntimeException(
                    'Tambahkan UNIQUE INDEX pada nik_lookup sebelum import.'
                );
            }

            if (
                ($invalidNik > 0 || $duplicateRows > 0) &&
                !$skipProblematic
            ) {
                throw new RuntimeException(
                    'Ada NIK bermasalah. Gunakan --skip-problematic untuk mengimpor hanya NIK valid dan unik.'
                );
            }

            $clean = static function ($value): ?string {
                if ($value === null) {
                    return null;
                }

                $value = trim((string) $value);
                $value = preg_replace('/\s+/u', ' ', $value);

                return $value === '' ? null : $value;
            };

            $imported = 0;
            $skippedInvalid = 0;
            $skippedDuplicate = 0;
            $processed = 0;

            $this->newLine();
            $this->info('Memulai import master...');
            $this->output->progressStart($totalStaging);

            DB::table($source)->chunkById(
                $chunkSize,
                function ($rows) use (
                    $target,
                    $fields,
                    $nikSecurity,
                    $duplicateSet,
                    $clean,
                    &$imported,
                    &$skippedInvalid,
                    &$skippedDuplicate,
                    &$processed
                ) {
                    $batch = [];
                    $now = now();

                    foreach ($rows as $row) {
                        $nik = trim((string) ($row->nik ?? ''));

                        if (!preg_match('/^[0-9]{16}$/D', $nik)) {
                            $skippedInvalid++;
                            continue;
                        }

                        if (isset($duplicateSet['n:' . $nik])) {
                            $skippedDuplicate++;
                            continue;
                        }

                        $data = [];

                        foreach ($fields as $field) {
                            $data[$field] = $clean(
                                $row->{$field} ?? null
                            );
                        }

                        $baris = trim(
                            (string) ($row->_baris_sumber ?? '')
                        );

                        $data['_baris_sumber'] = (
                            $baris !== '' &&
                            ctype_digit($baris) &&
                            strlen($baris) <= 18
                        )
                            ? (int) $baris
                            : null;

                        $data['nik_encrypted'] = $nikSecurity->encrypt(
                            $nik
                        );

                        $data['nik_lookup'] = $nikSecurity->lookup(
                            $nik
                        );

                        $data['name'] = $data['nama'];
                        $data['family_card_number'] = $data['no_kk'];

                        $data['status'] = $data[
                            'keberadaan_dtsen_label'
                        ];

                        $data['status_didata'] = trim(
                            (string) $row->status_didata
                        );

                        $data['institution_id'] = null;
                        $data['instansi'] = null;
                        $data['created_at'] = $now;
                        $data['updated_at'] = $now;

                        $batch[] = $data;
                    }

                    if ($batch !== []) {
                        DB::transaction(function () use (
                            $target,
                            $batch
                        ) {
                            DB::table($target)->insert($batch);
                        }, 3);

                        $imported += count($batch);
                    }

                    $processed += $rows->count();

                    $this->output->progressAdvance(
                        $rows->count()
                    );
                },
                'id'
            );

            $this->output->progressFinish();

            $totalAfter = DB::table($target)->count();

            $this->newLine(2);

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Staging diproses', number_format($processed, 0, ',', '.')],
                    ['Berhasil import', number_format($imported, 0, ',', '.')],
                    ['NIK tidak valid dilewati', number_format($skippedInvalid, 0, ',', '.')],
                    ['Baris duplikat dilewati', number_format($skippedDuplicate, 0, ',', '.')],
                    ['Total master', number_format($totalAfter, 0, ',', '.')],
                ]
            );

            if (
                $processed !== $totalStaging ||
                $imported !== $eligible ||
                $skippedInvalid !== $invalidNik ||
                $skippedDuplicate !== $duplicateRows ||
                $totalAfter !== $eligible
            ) {
                throw new RuntimeException(
                    'Hasil import tidak sesuai dengan perhitungan awal.'
                );
            }

            $this->newLine();
            $this->info('Import master selesai.');
            $this->info(
                'NIK terenkripsi dan lookup HMAC berhasil dibuat.'
            );
            $this->info(
                'Data bermasalah tetap tersimpan di staging.'
            );

            return self::SUCCESS;
        } catch (Throwable $error) {
            $this->newLine();
            $this->error('Import gagal.');
            $this->error($error->getMessage());

            $this->warn(
                'Jika import sudah dimulai, sebagian batch mungkin telah tersimpan di master. Jangan ulangi import tanpa memeriksa jumlah data.'
            );

            return self::FAILURE;
        }
    }
}
