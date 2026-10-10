<?php

namespace App\Exports;

use App\Models\Institution;
use App\Services\NikSecurityService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class MonitoringExport
{
    private const SUDAH_SQL = "(LOWER(TRIM(COALESCE(status_didata, ''))) IN ('sudah didata', 'sudah', 'ditemukan', 'baru', '1') OR REGEXP_REPLACE(LOWER(TRIM(COALESCE(respSE26_keberadaan_klrg, ''))), '^[0-9]+[.][[:space:]]*', '') IN ('ditemukan', 'baru'))";

    private array $filters;
    private ?string $institutionName = null;
    private bool $institutionExists = true;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;

        if (!empty($filters['institution'])) {
            $name = Institution::query()
                ->whereKey($filters['institution'])
                ->value('name');

            $this->institutionExists = $name !== null;
            $this->institutionName = $name;
        }
    }

    public function download(string $filename): BinaryFileResponse
    {
        set_time_limit(0);

        $directory = storage_path('app/private/monitoring-exports');

        File::ensureDirectoryExists($directory, 0700, true);

        $path = $directory . DIRECTORY_SEPARATOR . Str::uuid() . '.xlsx';

        $options = new Options();
        $options->setTempFolder($directory);

        $writer = new Writer($options);
        $opened = false;

        try {
            $writer->openToFile($path);
            $opened = true;

            $this->writeMaster($writer);

            $writer->addNewSheetAndMakeItCurrent();

            $this->writeManual($writer);

            $writer->close();
            $opened = false;

            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
                'X-Content-Type-Options' => 'nosniff',
            ])->deleteFileAfterSend(true);
        } catch (Throwable $exception) {
            if ($opened) {
                try {
                    $writer->close();
                } catch (Throwable) {
                }
            }

            if (is_file($path)) {
                @unlink($path);
            }

            throw $exception;
        }
    }

    private function header(Writer $writer, string $sheetName, array $columns): void
    {
        $sheet = $writer->getCurrentSheet();

        $sheet->setName($sheetName);
        $sheet->setColumnWidthForRange(20, 1, count($columns));

        $style = new Style();
        $style->setFontBold();
        $style->setFontColor(Color::WHITE);
        $style->setBackgroundColor(Color::rgb(29, 78, 216));
        $style->setFontSize(11);

        $writer->addRow(Row::fromValues($columns, $style));
    }

    private function writeMaster(Writer $writer): void
    {
        $this->header($writer, 'master_employees', [
            'No',
            'Nama',
            'Nama Kepala Keluarga',
            'NIK',
            'NIP',
            'Nomor KK',
            'Kecamatan',
            'Kelurahan',
            'RT/RW',
            'ID SLS/SubSLS',
            'Alamat',
            'Status Pendataan',
            'Status',
            'Assignment ID',
            'Code Identity',
            'Status Assignment',
            'Instansi',
            'Profesi',
            'Status Pekerjaan',
            'Profesi Lainnya',
            'Assignment ID SE2026',
            'Nomor KK SE2026',
            'Nama SE2026',
            'Code Identity SE2026',
            'Keberadaan Keluarga SE2026',
        ]);

        if (
            ($this->filters['source'] ?? 'all') === 'manual' ||
            ($this->filters['status'] ?? '') === 'Perlu Tindak Lanjut'
        ) {
            return;
        }

        $phases = match ($this->filters['status'] ?? '') {
            'Sudah Didata' => ['sudah'],
            'Belum Didata' => ['belum'],
            default => ['belum', 'sudah'],
        };

        $security = app(NikSecurityService::class);
        $number = 0;

        foreach ($phases as $phase) {
            $query = DB::table('master_employees');

            $query->whereRaw(
                $phase === 'sudah'
                    ? self::SUDAH_SQL
                    : 'NOT ' . self::SUDAH_SQL
            );

            $this->applyMasterFilters($query);

            $query->chunkById(500, function ($rows) use (
                $writer,
                $security,
                $phase,
                &$number
            ): void {
                foreach ($rows as $employee) {
                    $number++;

                    $writer->addRow(Row::fromValues($this->asText([
                        $number,
                        $employee->nama ?? $employee->name ?? '',
                        $employee->nama_kepala_keluarga,
                        $this->decryptNik(
                            $employee->nik_encrypted,
                            $security,
                            $employee->id
                        ),
                        $employee->nip,
                        $employee->no_kk,
                        $employee->kecamatan,
                        $employee->kelurahan,
                        $employee->rt_rw,
                        $employee->idsubsls,
                        $employee->alamat,
                        $phase === 'sudah'
                            ? 'Sudah Didata'
                            : 'Belum Didata',
                        $employee->status,
                        $employee->assignment_id,
                        $employee->code_identity,
                        $employee->assignment_status_alias,
                        $employee->instansi,
                        $employee->profesi,
                        $employee->status_kerja_label,
                        $employee->profesi_lainnya,
                        $employee->respSE26_assignment_id,
                        $employee->respSE26_no_kk,
                        $employee->respSE26_nama,
                        $employee->respSE26_code_identity,
                        $employee->respSE26_keberadaan_klrg,
                    ])));
                }
            }, 'id');
        }
    }

    private function writeManual(Writer $writer): void
    {
        $this->header($writer, 'manual_registrations', [
            'No',
            'Nama',
            'NIK',
            'NIP',
            'Gmail',
            'Instansi',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan/Desa',
            'RW',
            'RT',
            'Status Pendataan',
            'Persetujuan Email',
            'Tanggal Pendaftaran',
        ]);

        if (
            ($this->filters['source'] ?? 'all') === 'master' ||
            !in_array(
                $this->filters['status'] ?? '',
                ['', 'Perlu Tindak Lanjut'],
                true
            )
        ) {
            return;
        }

        $query = DB::table('manual_registrations');

        $this->applyManualFilters($query);

        $security = app(NikSecurityService::class);
        $number = 0;

        $query->chunkById(500, function ($rows) use (
            $writer,
            $security,
            &$number
        ): void {
            foreach ($rows as $employee) {
                $number++;

                $writer->addRow(Row::fromValues($this->asText([
                    $number,
                    $employee->name,
                    $this->decryptNik(
                        $employee->nik_encrypted,
                        $security,
                        $employee->id
                    ),
                    $employee->nip,
                    $employee->email,
                    $employee->institution,
                    $employee->province_name,
                    $employee->regency_name,
                    $employee->district_name,
                    $employee->village_name,
                    $employee->rw,
                    $employee->rt,
                    'Perlu Tindak Lanjut',
                    $employee->email_consent_at ? 'Ya' : 'Tidak',
                    $employee->created_at,
                ])));
            }
        }, 'id');
    }

    private function applyMasterFilters(Builder $query): void
    {
        if (!$this->institutionExists) {
            $query->whereRaw('1 = 0');
            return;
        }

        if (!empty($this->filters['search'])) {
            $search = $this->likeValue($this->filters['search']);

            $query->where(function (Builder $builder) use ($search) {
                foreach ([
                    'nama',
                    'name',
                    'nama_kepala_keluarga',
                    'nip',
                    'assignment_id',
                    'idsubsls',
                    'rt_rw',
                    'kecamatan',
                    'kelurahan',
                    'instansi',
                    'status_didata',
                ] as $column) {
                    $builder->orWhere($column, 'like', $search);
                }
            });
        }

        if (!empty($this->filters['institution'])) {
            $query->where(function (Builder $builder) {
                $builder
                    ->where('institution_id', $this->filters['institution'])
                    ->orWhere('instansi', $this->institutionName);
            });
        }

        if (!empty($this->filters['kecamatan'])) {
            $query->where(
                'kecamatan',
                $this->filters['kecamatan']
            );
        }

        if (!empty($this->filters['kelurahan'])) {
            $query->where(
                'kelurahan',
                $this->filters['kelurahan']
            );
        }
    }

    private function applyManualFilters(Builder $query): void
    {
        if (!$this->institutionExists) {
            $query->whereRaw('1 = 0');
            return;
        }

        if (!empty($this->filters['search'])) {
            $search = $this->likeValue($this->filters['search']);

            $query->where(function (Builder $builder) use ($search) {
                foreach ([
                    'name',
                    'nip',
                    'institution',
                    'province_name',
                    'regency_name',
                    'district_name',
                    'village_name',
                    'rt',
                    'rw',
                ] as $column) {
                    $builder->orWhere($column, 'like', $search);
                }
            });
        }

        if (!empty($this->filters['institution'])) {
            $query->where(
                'institution',
                $this->institutionName
            );
        }

        if (!empty($this->filters['kecamatan'])) {
            $query->where(
                'district_name',
                $this->filters['kecamatan']
            );
        }

        if (!empty($this->filters['kelurahan'])) {
            $query->where(
                'village_name',
                $this->filters['kelurahan']
            );
        }
    }

    private function decryptNik(
        ?string $encrypted,
        NikSecurityService $security,
        int|string $id
    ): string {
        if (!$encrypted) {
            throw new RuntimeException(
                'NIK kosong pada baris ' . $id
            );
        }

        $value = $security->decrypt($encrypted);

        if (!preg_match('/^[0-9]{16}$/D', $value)) {
            throw new RuntimeException(
                'Format NIK tidak valid pada baris ' . $id
            );
        }

        return $value;
    }

    private function asText(array $values): array
    {
        return array_map(static function ($value): string {
            $text = trim((string) ($value ?? ''));

            return preg_match('/^[=+\-@\t\r]/u', $text)
                ? "'" . $text
                : $text;
        }, $values);
    }

    private function likeValue(string $value): string
    {
        return '%' . addcslashes(trim($value), '%_\\') . '%';
    }
}
