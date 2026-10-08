<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class ImportMasterStaging extends Command
{

    protected $signature = 'import:master-staging {file=storage/app/import/master_baru.csv}';

    protected $description = 'Import CSV master pegawai ke tabel staging baru';

    public function handle(): int
    {
        set_time_limit(0);

        $file = $this->argument('file');
        $path = realpath($file) ?: realpath(base_path($file));

        if (!$path || !is_readable($path)) {
            $this->error('File CSV tidak ditemukan atau tidak dapat dibaca.');
            return self::FAILURE;
        }

        $source = 'staging_master_employees';
        $target = 'staging_master_employees_import_20261008';

        $expected = [
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

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            $this->error('Gagal membuka file CSV.');
            return self::FAILURE;
        }

        $recordNumber = 1;
        $total = 0;
        $emptyNik = 0;
        $invalidNik = 0;
        $emptyStatus = 0;
        $batchSize = 250;

        try {
            if (!Schema::hasTable($source)) {
                throw new RuntimeException(
                    "Tabel sumber {$source} tidak ditemukan."
                );
            }

            if (Schema::hasTable($target)) {
                throw new RuntimeException(
                    "Tabel {$target} sudah ada. Import dibatalkan agar data tidak tertimpa."
                );
            }

            $columns = Schema::getColumnListing($source);
            $missingColumns = array_diff($expected, $columns);

            if (!empty($missingColumns)) {
                throw new RuntimeException(
                    'Kolom staging tidak tersedia: ' .
                    implode(', ', $missingColumns)
                );
            }

            $header = fgetcsv($handle, 0, ',', '"', '');

            if ($header === false) {
                throw new RuntimeException(
                    'Header CSV tidak ditemukan.'
                );
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
                $this->error('Header CSV tidak sesuai.');

                $this->line(
                    'Kolom CSV: ' . implode(', ', $header)
                );

                $this->line(
                    'Kolom yang diharapkan: ' .
                    implode(', ', $expected)
                );

                return self::FAILURE;
            }

            $this->info('Header CSV berhasil divalidasi.');

            DB::statement(
                "CREATE TABLE `{$target}` LIKE `{$source}`"
            );

            $this->info(
                "Tabel {$target} berhasil dibuat."
            );

            $this->info('Memulai proses import...');
            $this->newLine();

            $batch = [];

            while (
                ($record = fgetcsv($handle, 0, ',', '"', '')) !== false
            ) {
                $recordNumber++;

                if (
                    count($record) === 1 &&
                    ($record[0] === null || $record[0] === '')
                ) {
                    continue;
                }

                if (count($record) !== count($expected)) {
                    throw new RuntimeException(
                        "Jumlah kolom tidak sesuai pada record {$recordNumber}. " .
                        "Ditemukan " . count($record) .
                        " kolom, seharusnya " . count($expected) . "."
                    );
                }

                $values = array_map(
                    fn ($value) => $value ?? '',
                    $record
                );

                $data = array_combine($expected, $values);

                $nik = trim((string) $data['nik']);
                $status = trim((string) $data['status_didata']);

                if ($nik === '') {
                    $emptyNik++;
                }

                if (!preg_match('/^[0-9]{16}$/D', $nik)) {
                    $invalidNik++;
                }

                if ($status === '') {
                    $emptyStatus++;
                }

                $batch[] = $data;

                if (count($batch) >= $batchSize) {
                    DB::table($target)->insert($batch);

                    $total += count($batch);
                    $batch = [];

                    if ($total % 10000 === 0) {
                        $this->info(
                            number_format($total, 0, ',', '.') .
                            ' data berhasil diimport.'
                        );
                    }
                }
            }

            if (!empty($batch)) {
                DB::table($target)->insert($batch);
                $total += count($batch);
            }

            $stored = DB::table($target)->count();

            $this->newLine();
            $this->info('Proses import selesai.');
            $this->newLine();

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Total diproses', number_format($total, 0, ',', '.')],
                    ['Total tersimpan', number_format($stored, 0, ',', '.')],
                    ['NIK kosong', number_format($emptyNik, 0, ',', '.')],
                    ['NIK tidak valid', number_format($invalidNik, 0, ',', '.')],
                    ['Status didata kosong', number_format($emptyStatus, 0, ',', '.')],
                ]
            );

            if ($total === 0) {
                throw new RuntimeException(
                    'Tidak ada data CSV yang berhasil diproses.'
                );
            }

            if ($total !== $stored) {
                throw new RuntimeException(
                    'Jumlah data diproses dan tersimpan tidak sama.'
                );
            }

            $this->info(
                'Import berhasil. Tabel master_employees tidak diubah.'
            );

            return self::SUCCESS;
        } catch (Throwable $error) {
            $this->newLine();
            $this->error('Proses import gagal.');

            $this->line(
                'Record terakhir: ' .
                number_format($recordNumber, 0, ',', '.')
            );

            $this->line(
                'Data berhasil tersimpan sebelum error: ' .
                number_format($total, 0, ',', '.')
            );

            if ($error instanceof QueryException) {
                $this->error(
                    'SQLSTATE: ' .
                    ($error->errorInfo[0] ?? $error->getCode())
                );

                $this->error(
                    'Kode MySQL: ' .
                    ($error->errorInfo[1] ?? '-')
                );

                $this->error(
                    'Pesan MySQL: ' .
                    ($error->errorInfo[2] ?? 'Query database gagal.')
                );
            } else {
                $this->error($error->getMessage());
            }

            $this->warn(
                'Data master tidak diubah.'
            );

            return self::FAILURE;
        } finally {
            fclose($handle);
        }
    }
}
