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

    protected $status;

    protected $employeeId;

    protected $canSeeData;

    public function __construct(
        ?string $from = null,
        ?string $to = null,
        ?string $status = null,
        ?int $employeeId = null,
        bool $canSeeData = true
    ) {
        $this->canSeeData = $canSeeData;
        $this->employeeId = $employeeId;
        $this->from = $from;
        $this->status = $status;
        $this->to = $to;
    }

    public function collection(): Collection
    {
        return Attendance::with(['employee.position', 'employee.shift'])
            ->when(! $this->canSeeData, fn ($query) => $query->whereRaw('1 = 0'))
            ->whereDate('date', '<=', now()->toDateString())
            ->when($this->employeeId, fn ($query, $employeeId) => $query->where('employee_id', $employeeId))
            ->when($this->status, fn ($query, $status) => $query->where('status', $status))
            ->when($this->from, fn ($query) => $query->whereDate('date', '>=', $this->from))
            ->when($this->to, fn ($query) => $query->whereDate('date', '<=', $this->to))
            ->orderByDesc('date')
            ->orderBy('employee_id')
            ->orderBy('id')
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
            $attendance->date->translatedFormat('l, j F Y'),
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
