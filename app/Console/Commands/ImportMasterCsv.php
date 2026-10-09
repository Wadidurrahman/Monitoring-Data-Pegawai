<?php

namespace App\Console\Commands;

use App\Services\NikSecurityService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class ImportMasterCsv extends Command
{
    protected $signature = 'master:import-csv {file=storage/app/import/master_baru.csv} {--execute} {--batch=100}';

    protected $description = 'Import CSV langsung ke master pegawai dengan enkripsi dan HMAC NIK';

    public function handle(NikSecurityService $security): int
    {
        set_time_limit(0);

        $path = realpath(base_path($this->argument('file')));
        $execute = (bool) $this->option('execute');
        $batchSize = (int) $this->option('batch');
        $target = 'master_employees';

        $columns = [
            'idsubsls',
            'rt_rw',
            'kecamatan',
            'kelurahan',
            'nama_kepala_keluarga',
            'nama',
            'nik',
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
            'status_didata',
        ];

        try {
            if (!$path || !is_readable($path)) {
                throw new RuntimeException('File CSV tidak ditemukan.');
            }

            if ($batchSize < 1 || $batchSize > 500) {
                throw new RuntimeException('Batch harus antara 1 sampai 500.');
            }

            if (!Schema::hasTable($target)) {
                throw new RuntimeException('Tabel master_employees tidak ditemukan.');
            }

            $required = array_merge(
                array_diff($columns, ['nik']),
                [
                    'nik_encrypted',
                    'nik_lookup',
                    'name',
                    'family_card_number',
                    'status',
                    'institution_id',
                    'instansi',
                    'created_at',
                    'updated_at',
                ]
            );

            $missing = array_diff(
                $required,
                Schema::getColumnListing($target)
            );

            if ($missing !== []) {
                throw new RuntimeException(
                    'Kolom master tidak ditemukan: ' . implode(', ', $missing)
                );
            }

            $security->lookup('0000000000000000');

            $existingSamples = DB::table($target)
                ->whereNotNull('nik_lookup')
                ->whereNotNull('nik_encrypted')
                ->select('nik_lookup', 'nik_encrypted')
                ->limit(5)
                ->get();

            foreach ($existingSamples as $sample) {
                $nik = $security->decrypt($sample->nik_encrypted);

                if (!hash_equals(
                    $sample->nik_lookup,
                    $security->lookup($nik)
                )) {
                    throw new RuntimeException(
                        'Kunci keamanan tidak sesuai dengan data master lama.'
                    );
                }
            }

            $this->info('Memvalidasi file CSV...');

            $handle = $this->openCsv($path, $columns);

            $nikIndex = array_search('nik', $columns, true);
            $statusIndex = array_search('status_didata', $columns, true);

            $seen = [];
            $total = 0;
            $invalid = 0;
            $emptyStatus = 0;

            try {
                while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                    if (count($row) === 1 && trim((string) $row[0]) === '') {
                        continue;
                    }

                    $total++;

                    if (count($row) !== count($columns)) {
                        throw new RuntimeException(
                            "Jumlah kolom CSV tidak sesuai pada record {$total}."
                        );
                    }

                    $nik = trim((string) $row[$nikIndex]);
                    $status = trim((string) $row[$statusIndex]);

                    if ($status === '') {
                        $emptyStatus++;
                    }

                    if (!preg_match('/^[0-9]{16}$/D', $nik)) {
                        $invalid++;
                        continue;
                    }

                    $lookup = $security->lookup($nik);
                    $seen[$lookup] = ($seen[$lookup] ?? 0) + 1;
                }
            } finally {
                fclose($handle);
            }

            $duplicates = [];
            $duplicateRows = 0;

            foreach ($seen as $lookup => $count) {
                if ($count > 1) {
                    $duplicates[$lookup] = true;
                    $duplicateRows += $count;
                }
            }

            unset($seen);

            $eligible = $total - $invalid - $duplicateRows;
            $masterBefore = DB::table($target)->count();

            $this->newLine();

            $this->table(
                ['Pemeriksaan', 'Jumlah'],
                [
                    ['Total CSV', number_format($total, 0, ',', '.')],
                    ['NIK tidak valid', number_format($invalid, 0, ',', '.')],
                    ['Baris NIK duplikat', number_format($duplicateRows, 0, ',', '.')],
                    ['Status kosong', number_format($emptyStatus, 0, ',', '.')],
                    ['Kandidat import', number_format($eligible, 0, ',', '.')],
                    ['Master saat ini', number_format($masterBefore, 0, ',', '.')],
                ]
            );

            if ($total === 0 || $eligible === 0) {
                throw new RuntimeException('Tidak ada kandidat data yang valid.');
            }

            if ($emptyStatus > 0) {
                throw new RuntimeException(
                    'Status pendataan kosong ditemukan. Periksa CSV dahulu.'
                );
            }

            $indexes = collect(
                DB::select("SHOW INDEX FROM `{$target}`")
            )->groupBy('Key_name');

            $hasUniqueLookup = $indexes->contains(function ($parts) {
                if ($parts->count() !== 1) {
                    return false;
                }

                $index = $parts->first();

                return (int) $index->Non_unique === 0
                    && $index->Column_name === 'nik_lookup';
            });

            $this->line(
                'Unique index nik_lookup: ' .
                ($hasUniqueLookup ? 'Tersedia' : 'Belum tersedia')
            );

            if (!$execute) {
                $this->info('Validasi selesai. Database belum diubah.');
                return self::SUCCESS;
            }

            if (!$hasUniqueLookup) {
                throw new RuntimeException(
                    'Tambahkan UNIQUE INDEX pada nik_lookup sebelum import.'
                );
            }

            $handle = $this->openCsv($path, $columns);

            $inserted = 0;
            $skippedExisting = 0;
            $processed = 0;
            $batch = [];

            $clean = static function ($value): ?string {
                if ($value === null) {
                    return null;
                }

                $value = trim((string) $value);
                return $value === '' ? null : $value;
            };

            $saveBatch = function () use (
                &$batch,
                &$inserted,
                &$skippedExisting,
                $target
            ): void {
                if ($batch === []) {
                    return;
                }

                $lookups = array_column($batch, 'nik_lookup');

                $existing = DB::table($target)
                    ->whereIn('nik_lookup', $lookups)
                    ->pluck('nik_lookup')
                    ->all();

                $existingMap = array_fill_keys($existing, true);
                $newRows = [];

                foreach ($batch as $data) {
                    if (isset($existingMap[$data['nik_lookup']])) {
                        $skippedExisting++;
                    } else {
                        $newRows[] = $data;
                    }
                }

                if ($newRows !== []) {
                    DB::transaction(function () use ($target, $newRows) {
                        DB::table($target)->insert($newRows);
                    }, 3);

                    $inserted += count($newRows);
                }

                $batch = [];
            };

            $this->newLine();
            $this->info('Memulai import langsung ke master...');

            try {
                while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                    if (count($row) === 1 && trim((string) $row[0]) === '') {
                        continue;
                    }

                    $processed++;

                    if (count($row) !== count($columns)) {
                        throw new RuntimeException(
                            "Struktur CSV berubah pada record {$processed}."
                        );
                    }

                    $values = array_combine($columns, $row);
                    $nik = trim((string) ($values['nik'] ?? ''));

                    if (!preg_match('/^[0-9]{16}$/D', $nik)) {
                        continue;
                    }

                    $lookup = $security->lookup($nik);

                    if (isset($duplicates[$lookup])) {
                        continue;
                    }

                    $data = [];

                    foreach ($columns as $column) {
                        if ($column === 'nik') {
                            continue;
                        }

                        $data[$column] = $clean($values[$column]);
                    }

                    $sourceRow = $data['_baris_sumber'];

                    $data['_baris_sumber'] = (
                        $sourceRow !== null &&
                        ctype_digit($sourceRow) &&
                        strlen($sourceRow) <= 18
                    ) ? (int) $sourceRow : null;

                    $data['nik_encrypted'] = $security->encrypt($nik);
                    $data['nik_lookup'] = $lookup;
                    $data['name'] = $data['nama'];
                    $data['family_card_number'] = $data['no_kk'];
                    $data['status'] = $data['keberadaan_dtsen_label'];

                    $data['institution_id'] = null;
                    $data['instansi'] = null;

                    $data['created_at'] = now();
                    $data['updated_at'] = now();

                    $batch[] = $data;

                    if (count($batch) >= $batchSize) {
                        $saveBatch();
                    }

                    if ($processed % 10000 === 0) {
                        $this->line(
                            number_format($processed, 0, ',', '.') .
                            ' record telah diproses.'
                        );
                    }
                }

                $saveBatch();
            } finally {
                fclose($handle);
            }

            $masterAfter = DB::table($target)->count();

            $this->newLine();

            $this->table(
                ['Hasil', 'Jumlah'],
                [
                    ['CSV diproses', number_format($processed, 0, ',', '.')],
                    ['Data baru ditambahkan', number_format($inserted, 0, ',', '.')],
                    ['Sudah ada di master', number_format($skippedExisting, 0, ',', '.')],
                    ['Total master akhir', number_format($masterAfter, 0, ',', '.')],
                ]
            );

            if ($processed !== $total) {
                throw new RuntimeException(
                    'Jumlah record CSV berubah selama proses import.'
                );
            }

            $this->info('Import CSV ke master selesai.');

            return self::SUCCESS;
        } catch (Throwable $error) {
            $this->error($error->getMessage());
            $this->warn(
                'Jika import terhenti, jalankan ulang setelah memeriksa penyebabnya. Data yang sudah berhasil masuk akan dilewati.'
            );

            return self::FAILURE;
        }
    }

    private function openCsv(string $path, array $expected)
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('File CSV tidak dapat dibuka.');
        }

        $header = fgetcsv($handle, 0, ',', '"', '');

        if ($header === false) {
            fclose($handle);
            throw new RuntimeException('Header CSV tidak ditemukan.');
        }

        $header = array_map(
            fn ($value) => trim((string) $value),
            $header
        );

        $header[0] = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $header[0]
        );

        if ($header !== $expected) {
            fclose($handle);
            throw new RuntimeException(
                'Header CSV tidak sesuai dengan struktur yang diharapkan.'
            );
        }

        return $handle;
    }
}
