<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AttendanceHolidayService
{
    public function syncYear(int $year): int
    {
        $dates = $this->holidayDates($year);
        $created = 0;

        Employee::query()->select('id')->chunkById(100, function ($employees) use ($dates, &$created): void {
            foreach ($employees as $employee) {
                $created += $this->syncEmployeeDates($employee, $dates);
            }
        });

        return $created;
    }

    public function syncEmployeeYear(Employee $employee, int $year): int
    {
        return $this->syncEmployeeDates($employee, $this->holidayDates($year));
    }

    public function syncDate(CarbonInterface|string $date): int
    {
        $date = CarbonImmutable::parse($date)->startOfDay();
        $reason = $this->holidayReason($date);

        if (! $reason) {
            return 0;
        }

        $created = 0;

        Employee::query()->select('id')->chunkById(100, function ($employees) use ($date, $reason, &$created): void {
            foreach ($employees as $employee) {
                $attendance = Attendance::firstOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date->toDateString()],
                    ['status' => 'Libur', 'note' => $reason]
                );
                $created += $attendance->wasRecentlyCreated ? 1 : 0;
            }
        });

        return $created;
    }

    public function holidayDates(int $year): Collection
    {
        $dates = collect();

        foreach (config('indonesian_holidays', [])[$year]['national'] ?? [] as $holiday) {
            $dates->put($holiday['date'], 'Libur Nasional: '.$holiday['name']);
        }

        if (Schema::hasTable('holidays')) {
            Holiday::whereYear('date', $year)
                ->where('type', 'national')
                ->orderBy('date')
                ->get()
                ->each(fn (Holiday $holiday) => $dates->put(
                    $holiday->date->toDateString(),
                    'Libur Nasional: '.$holiday->name
                ));
        }

        foreach (CarbonPeriod::create("$year-01-01", "$year-12-31") as $date) {
            if ($date->isSunday() && ! $dates->has($date->toDateString())) {
                $dates->put($date->toDateString(), 'Libur Minggu');
            }
        }

        return $dates->sortKeys();
    }

    private function holidayReason(CarbonInterface $date): ?string
    {
        return $this->holidayDates($date->year)->get($date->toDateString());
    }

    private function syncEmployeeDates(Employee $employee, Collection $dates): int
    {
        $created = 0;

        foreach ($dates as $date => $reason) {
            $attendance = Attendance::firstOrCreate(
                ['employee_id' => $employee->id, 'date' => $date],
                ['status' => 'Libur', 'note' => $reason]
            );
            $created += $attendance->wasRecentlyCreated ? 1 : 0;
        }

        return $created;
    }
}
