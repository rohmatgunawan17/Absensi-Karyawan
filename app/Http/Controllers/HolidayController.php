<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $year = (int) $request->integer('year', now()->year);
        $calendars = config('indonesian_holidays', []);
        $calendar = $calendars[$year] ?? null;

        if (! $calendar) {
            return response()->json([]);
        }

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

        return response()->json($national->concat($collectiveLeave)->values());
    }
}
