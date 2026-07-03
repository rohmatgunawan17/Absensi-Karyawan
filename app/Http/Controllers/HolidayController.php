<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HolidayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $year = (int) $request->integer('year', now()->year);
        $calendars = config('indonesian_holidays', []);
        $calendar = $calendars[$year] ?? ['national' => [], 'collective_leave' => []];

        $national = collect($calendar['national'])->map(fn (array $holiday): array => [
            'title' => $holiday['name'],
            'start' => $holiday['date'],
            'allDay' => true,
            'backgroundColor' => '#dc2626',
            'borderColor' => '#dc2626',
            'extendedProps' => ['type' => 'Libur Nasional'],
        ]);

        $collectiveLeave = collect($calendar['collective_leave'])->map(fn (array $holiday): array => [
            'title' => $holiday['name'],
            'start' => $holiday['date'],
            'allDay' => true,
            'backgroundColor' => '#d97706',
            'borderColor' => '#d97706',
            'extendedProps' => ['type' => 'Cuti Bersama'],
        ]);

        $databaseHolidays = Schema::hasTable('holidays')
            ? Holiday::whereYear('date', $year)->get()->map(fn (Holiday $holiday): array => [
                'title' => $holiday->name,
                'start' => $holiday->date->toDateString(),
                'allDay' => true,
                'backgroundColor' => $holiday->type === 'national' ? '#dc2626' : '#d97706',
                'borderColor' => $holiday->type === 'national' ? '#dc2626' : '#d97706',
                'extendedProps' => [
                    'type' => $holiday->type === 'national' ? 'Libur Nasional' : 'Cuti Bersama',
                    'source' => $holiday->source,
                ],
            ])
            : collect();

        return response()->json(
            $national->concat($collectiveLeave)
                ->concat($databaseHolidays)
                ->unique(fn (array $holiday) => $holiday['start'].'|'.$holiday['title'].'|'.$holiday['extendedProps']['type'])
                ->values()
        );
    }
}
