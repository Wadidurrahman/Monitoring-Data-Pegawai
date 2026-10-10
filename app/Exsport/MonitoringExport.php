<?php

namespace App\Exports;

use App\Models\MasterEmployee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithCustomValueBinder
{
    protected array $filters = [];
    protected int $number = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function bindValue(Cell $cell, $value): bool
    {
        $cell->setValueExplicit((string) ($value ?? ''), DataType::TYPE_STRING);
        return true;
    }

    public function query()
    {
        $query = MasterEmployee::query()->orderBy('id');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nama_kepala_keluarga', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('assignment_id', 'like', "%{$search}%")
                    ->orWhere('idsubsls', 'like', "%{$search}%")
                    ->orWhere('rt_rw', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%")
                    ->orWhere('kelurahan', 'like', "%{$search}%")
                    ->orWhere('instansi', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('sudah_didata', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['institution'])) {
            $query->where('institution_id', $this->filters['institution']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('sudah_didata', $this->filters['status']);
        }

        if (!empty($this->filters['kecamatan'])) {
            $query->where('kecamatan', $this->filters['kecamatan']);
        }

        if (!empty($this->filters['kelurahan'])) {
            $query->where('kelurahan', $this->filters['kelurahan']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
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
        ];
    }

    public function map($employee): array
    {
        $this->number++;

        return [
            $this->number,
            $employee->nama,
            $employee->nama_kepala_keluarga,
            'Dilindungi',
            $employee->nip ?? '',
            $employee->no_kk ?? '',
            $employee->kecamatan,
            $employee->kelurahan,
            $employee->rt_rw,
            $employee->idsubsls ?? '',
            $employee->alamat,
            $employee->sudah_didata,
            $employee->status,
            $employee->assignment_id ?? '',
            $employee->code_identity ?? '',
            $employee->assignment_status_alias,
            $employee->instansi,
            $employee->profesi,
            $employee->status_kerja_label,
            $employee->profesi_lainnya,
            $employee->respSE26_assignment_id ?? '',
            $employee->respSE26_no_kk ?? '',
            $employee->respSE26_nama,
            $employee->respSE26_code_identity ?? '',
            $employee->respSE26_keberadaan_klrg,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:Y1');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8, 'B' => 28, 'C' => 30, 'D' => 22, 'E' => 24,
            'F' => 22, 'G' => 20, 'H' => 22, 'I' => 14, 'J' => 20,
            'K' => 35, 'L' => 20, 'M' => 20, 'N' => 24, 'O' => 22,
            'P' => 24, 'Q' => 28, 'R' => 24, 'S' => 24, 'T' => 28,
            'U' => 26, 'V' => 24, 'W' => 28, 'X' => 26, 'Y' => 32,
        ];
    }

    public function title(): string
    {
        return 'Monitoring Pendataan';
    }
}
