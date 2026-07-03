<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Services\AttendanceHolidayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminHolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->integer('year', now()->addYear()->year);

        if ($year < 2020 || $year > 2100) {
            $year = now()->addYear()->year;
        }

        $holidays = Holiday::with('createdBy')
            ->whereYear('date', $year)
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        $years = Holiday::query()
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->year)
            ->push(now()->year, now()->addYear()->year)
            ->unique()
            ->sortDesc()
            ->values();

        return view('admin.holidays.index', compact('holidays', 'year', 'years'));
    }

    public function store(Request $request, AttendanceHolidayService $holidayService)
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:'.now()->startOfYear()->toDateString()],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:national,collective'],
            'source' => ['nullable', 'string', 'max:255'],
        ]);

        $holiday = Holiday::updateOrCreate(
            ['date' => $data['date'], 'name' => $data['name'], 'type' => $data['type']],
            ['source' => $data['source'], 'created_by' => $request->user()->id]
        );

        if ($holiday->type === 'national') {
            $holidayService->syncDate($holiday->date);
        }

        return back()->with('success', 'Hari libur berhasil disimpan dan disinkronkan.');
    }

    public function import(Request $request, AttendanceHolidayService $holidayService)
    {
        $data = $request->validate([
            'holiday_file' => ['nullable', 'required_without:holiday_data', 'file', 'mimes:csv,txt,xlsx,xls', 'max:2048'],
            'holiday_data' => ['nullable', 'required_without:holiday_file', 'string', 'max:2000000'],
        ]);

        $contents = $data['holiday_data'] ?? null;
        $rows = null;

        if ($contents) {
            $rows = $this->parseCsv($contents);
        }

        if (! $rows && $request->hasFile('holiday_file')) {
            $file = $request->file('holiday_file');
            $path = $file?->getRealPath();

            if (! $file?->isValid() || ! is_string($path) || $path === '' || ! is_file($path)) {
                throw ValidationException::withMessages([
                    'holiday_file' => 'File tidak dapat diproses. Pilih ulang file CSV.',
                ]);
            }

            $rows = in_array(strtolower($file->getClientOriginalExtension()), ['xlsx', 'xls'], true)
                ? $this->parseSpreadsheet($path)
                : $this->parseCsv((string) file_get_contents($path));
        }

        if (! $rows) {
            throw ValidationException::withMessages([
                'holiday_file' => 'File tidak memiliki data hari libur yang dapat diproses.',
            ]);
        }

        $years = collect();
        $imported = 0;

        DB::transaction(function () use ($rows, $request, &$imported, $years): void {
            foreach ($rows as $row) {
                $holiday = Holiday::updateOrCreate(
                    ['date' => $row['date'], 'name' => $row['name'], 'type' => $row['type']],
                    ['source' => $row['source'], 'created_by' => $request->user()->id]
                );

                $years->push($holiday->date->year);
                $imported++;
            }
        });

        foreach ($years->unique() as $year) {
            $holidayService->syncYear((int) $year);
        }

        return back()->with('success', "{$imported} hari libur berhasil diimpor dan disinkronkan.");
    }

    public function destroy(Holiday $holiday)
    {
        if ($holiday->type === 'national') {
            Attendance::whereDate('date', $holiday->date)
                ->where('status', 'Libur')
                ->whereNull('check_in')
                ->where('note', 'Libur Nasional: '.$holiday->name)
                ->delete();
        }

        $holiday->delete();

        return back()->with('success', 'Hari libur berhasil dihapus.');
    }

    private function parseCsv(string $contents): array
    {
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', trim($contents));
        $lines = preg_split('/\r\n|\r|\n/', $contents);
        $firstLine = $lines[0] ?? '';
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        $headers = array_map(fn ($header) => strtolower(trim($header)), str_getcsv($firstLine, $delimiter));
        $requiredHeaders = ['tanggal', 'nama'];

        foreach ($requiredHeaders as $header) {
            if (! in_array($header, $headers, true)) {
                throw ValidationException::withMessages([
                    'holiday_file' => 'CSV wajib memiliki kolom tanggal dan nama.',
                ]);
            }
        }

        $rows = [];
        $errors = [];

        foreach (array_slice($lines, 1) as $index => $line) {
            if (trim($line) === '') {
                continue;
            }

            $values = str_getcsv($line, $delimiter);
            $row = array_combine(
                $headers,
                array_slice(array_pad($values, count($headers), null), 0, count($headers))
            );
            $date = $this->parseDate((string) ($row['tanggal'] ?? ''));
            $name = trim((string) ($row['nama'] ?? ''));
            $type = $this->normalizeType((string) ($row['jenis'] ?? 'national'));

            if (! $date || $name === '' || ! $type) {
                $errors[] = $index + 2;

                continue;
            }

            $rows[] = [
                'date' => $date,
                'name' => $name,
                'type' => $type,
                'source' => trim((string) ($row['sumber'] ?? '')) ?: null,
            ];
        }

        if ($errors) {
            throw ValidationException::withMessages([
                'holiday_file' => 'Data tidak valid pada baris: '.implode(', ', $errors).'.',
            ]);
        }

        if (! $rows) {
            throw ValidationException::withMessages([
                'holiday_file' => 'CSV tidak memiliki data hari libur.',
            ]);
        }

        return $rows;
    }

    private function parseSpreadsheet(string $path): array
    {
        try {
            $sheetRows = IOFactory::load($path)->getActiveSheet()->toArray(null, true, true, false);
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'holiday_file' => 'File Excel tidak dapat dibaca. Gunakan XLSX/XLS yang valid.',
            ]);
        }

        $stream = fopen('php://temp', 'w+');

        foreach ($sheetRows as $row) {
            fputcsv($stream, $row);
        }

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        return $this->parseCsv((string) $contents);
    }

    private function parseDate(string $value): ?string
    {
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, trim($value));

                if ($date && $date->format($format) === trim($value)) {
                    return $date->toDateString();
                }
            } catch (\Throwable) {
                // Try the next supported format.
            }
        }

        return null;
    }

    private function normalizeType(string $value): ?string
    {
        return match (strtolower(trim($value))) {
            '', 'national', 'nasional', 'libur nasional' => 'national',
            'collective', 'cuti', 'cuti bersama' => 'collective',
            default => null,
        };
    }
}
