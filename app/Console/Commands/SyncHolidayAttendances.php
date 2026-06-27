<?php

namespace App\Console\Commands;

use App\Services\AttendanceHolidayService;
use Illuminate\Console\Command;

class SyncHolidayAttendances extends Command
{
    protected $signature = 'attendance:sync-holidays {--year= : Sinkronkan seluruh Minggu dan libur nasional pada tahun tertentu}';

    protected $description = 'Membuat data absensi Libur untuk hari Minggu dan libur nasional';

    public function handle(AttendanceHolidayService $service): int
    {
        $year = $this->option('year');
        $created = $year
            ? $service->syncYear((int) $year)
            : $service->syncDate(now());

        $this->info("Sinkronisasi selesai. {$created} data Libur dibuat.");

        return self::SUCCESS;
    }
}
