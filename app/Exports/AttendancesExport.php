<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendancesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from;
    protected $to;

    public function __construct(?string $from = null, ?string $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection(): Collection
    {
        return Attendance::with(['employee.position', 'employee.shift'])
            ->when($this->from, fn($query) => $query->whereDate('date', '>=', $this->from))
            ->when($this->to, fn($query) => $query->whereDate('date', '<=', $this->to))
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'NIP',
            'Nama',
            'Jabatan',
            'Shift',
            'Status',
            'Absen Masuk',
            'Absen Pulang',
            'Keterangan',
            'Latitude',
            'Longitude',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date->format('Y-m-d'),
            $attendance->employee->nip,
            $attendance->employee->name,
            $attendance->employee->position?->name,
            $attendance->employee->shift?->name,
            $attendance->status,
            optional($attendance->check_in)->format('H:i:s'),
            optional($attendance->check_out)->format('H:i:s'),
            $attendance->note,
            $attendance->latitude,
            $attendance->longitude,
        ];
    }
}
