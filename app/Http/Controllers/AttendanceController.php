<?php

namespace App\Http\Controllers;

use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');
        $request->validate([
            'from' => ['nullable', 'date_format:d/m/Y'],
            'to' => ['nullable', 'date_format:d/m/Y'],
        ]);
        $fromDate = $this->parseFilterDate($from);
        $toDate = $this->parseFilterDate($to);

        $attendances = Attendance::with(['employee.position', 'employee.shift'])
            ->whereDate('date', '<=', now()->toDateString())
            ->when($search, fn ($query) => $query->whereHas('employee', fn ($q) => $q->where('name', 'like', "%{$search}%")))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->orderByDesc('date')
            ->orderBy('employee_id')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('attendances.index', compact('attendances', 'search', 'status', 'from', 'to'));
    }

    public function history(Request $request)
    {
        $employee = $request->user()->employee;
        $type = $request->query('type', 'attendance');

        if (! in_array($type, ['attendance', 'holiday', 'all'], true)) {
            $type = 'attendance';
        }

        $request->validate([
            'from' => ['nullable', 'date_format:d/m/Y'],
            'to' => ['nullable', 'date_format:d/m/Y'],
        ]);
        $from = $request->query('from');
        $to = $request->query('to');
        $fromDate = $this->parseFilterDate($from);
        $toDate = $this->parseFilterDate($to);

        $attendances = Attendance::with(['employee.position', 'employee.shift'])
            ->where('employee_id', $employee->id)
            ->whereDate('date', '<=', now()->toDateString())
            ->when($type === 'attendance', fn ($query) => $query->where('status', '!=', 'Libur'))
            ->when($type === 'holiday', fn ($query) => $query->where('status', 'Libur'))
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->orderByDesc('date')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return view('attendances.history', compact('attendances', 'type'));
    }

    public function checkIn(Request $request)
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            return back()->with('error', 'Akun karyawan tidak ditemukan.');
        }

        $data = $request->validate([
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'selfie_photo' => 'nullable|required_without:selfie_data|image|mimes:jpg,jpeg,png|max:3072',
            'selfie_data' => 'nullable|required_without:selfie_photo|string|max:4300000',
            'note' => 'nullable|string|max:255',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpha',
        ]);

        $data = array_merge([
            'latitude' => null,
            'longitude' => null,
            'note' => null,
        ], $data);

        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($existingAttendance?->check_in && $existingAttendance->selfie_photo) {
            return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
        }

        try {
            $selfiePath = $this->storeSelfie($request);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors(['selfie_photo' => 'Foto selfie gagal disimpan. Silakan pilih ulang foto dan coba kembali.'])
                ->withInput($request->except('selfie_photo'));
        }

        if (! $selfiePath) {
            return back()
                ->withErrors(['selfie_photo' => 'File selfie tidak dapat diproses. Gunakan foto JPG atau PNG maksimal 3 MB.'])
                ->withInput($request->except('selfie_photo'));
        }

        try {
            $saved = DB::transaction(function () use ($data, $employee, $selfiePath): bool {
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', now()->toDateString())
                    ->lockForUpdate()
                    ->first();

                if ($attendance?->check_in && $attendance->selfie_photo) {
                    return false;
                }

                $attendance ??= new Attendance([
                    'employee_id' => $employee->id,
                    'date' => now()->toDateString(),
                ]);

                $attendance->fill([
                    'selfie_photo' => $selfiePath,
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'note' => $data['note'],
                    'status' => $data['status'],
                    'check_in' => now(),
                ])->save();

                return true;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($selfiePath);
            throw $exception;
        }

        if (! $saved) {
            Storage::disk('public')->delete($selfiePath);

            return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
        }

        return back()->with('success', 'Absensi masuk berhasil.');
    }

    private function storeSelfie(Request $request): string|false|null
    {
        $encodedPhoto = $request->input('selfie_data');

        if (is_string($encodedPhoto) && $encodedPhoto !== '') {
            if (! preg_match('/^data:image\/(jpeg|png);base64,([A-Za-z0-9+\/=]+)$/', $encodedPhoto, $matches)) {
                return null;
            }

            $contents = base64_decode($matches[2], true);

            if ($contents === false || strlen($contents) > 3 * 1024 * 1024) {
                return null;
            }

            $imageInfo = getimagesizefromstring($contents);
            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
            ];
            $mimeType = is_array($imageInfo) ? ($imageInfo['mime'] ?? null) : null;

            if (! isset($allowedMimeTypes[$mimeType])) {
                return null;
            }

            $path = 'attendances/'.Str::uuid().'.'.$allowedMimeTypes[$mimeType];

            return Storage::disk('public')->put($path, $contents) ? $path : false;
        }

        $photo = $request->file('selfie_photo');
        $temporaryPath = $photo?->getRealPath();

        if (! $photo?->isValid() || ! is_string($temporaryPath) || $temporaryPath === '' || ! is_file($temporaryPath)) {
            return null;
        }

        return $photo->store('attendances', 'public');
    }

    public function checkOut(Request $request)
    {
        $employee = $request->user()->employee;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (! $attendance || ! $attendance->check_in) {
            return back()->with('error', 'Absensi masuk belum ditemukan untuk hari ini.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Anda sudah melakukan absensi pulang hari ini.');
        }

        $attendance->update(['check_out' => now()]);

        return back()->with('success', 'Absensi pulang berhasil.');
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load('employee.position');

        return view('attendances.edit', compact('attendance'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Attendance::STATUSES)],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i', 'after_or_equal:check_in'],
            'latitude' => ['nullable', 'string', 'max:50'],
            'longitude' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $attendance->update([
            'status' => $data['status'],
            'check_in' => $data['check_in']
                ? Carbon::parse($attendance->date->toDateString().' '.$data['check_in'])
                : null,
            'check_out' => $data['check_out']
                ? Carbon::parse($attendance->date->toDateString().' '.$data['check_out'])
                : null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'note' => $data['note'],
        ]);

        return redirect()->route('attendances.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function report(Request $request)
    {
        $filters = $this->reportFilters($request);
        $from = $request->query('from');
        $to = $request->query('to');
        $status = $filters['status'];
        $selectedEmployeeId = $filters['employeeId'];
        $employees = $request->user()->isAdmin()
            ? Employee::orderBy('name')->get(['id', 'nip', 'name'])
            : collect();

        $attendances = $this->reportAttendanceQuery($filters)
            ->paginate(15)
            ->withQueryString();

        return view('attendances.report', compact(
            'attendances',
            'employees',
            'from',
            'selectedEmployeeId',
            'status',
            'to'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->reportFilters($request);
        $from = $request->query('from');
        $to = $request->query('to');
        $attendances = $this->reportAttendanceQuery($filters)->get();

        $pdf = Pdf::loadView('exports.attendance-pdf', compact('attendances', 'from', 'to'));

        return $pdf->download('rekap-absensi-'.now()->format('Ymd').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->reportFilters($request);

        return Excel::download(new AttendancesExport(
            $filters['fromDate'],
            $filters['toDate'],
            $filters['status'],
            $filters['employeeId'],
            $filters['canSeeData']
        ), 'rekap-absensi-'.now()->format('Ymd').'.xlsx');
    }

    private function parseFilterDate(?string $date): ?string
    {
        return $date ? Carbon::createFromFormat('d/m/Y', $date)->toDateString() : null;
    }

    private function reportFilters(Request $request): array
    {
        $rules = [
            'from' => ['nullable', 'date_format:d/m/Y'],
            'to' => ['nullable', 'date_format:d/m/Y'],
            'status' => ['nullable', 'in:'.implode(',', Attendance::STATUSES)],
        ];

        if ($request->user()->isAdmin()) {
            $rules['employee_id'] = ['nullable', 'integer', 'exists:employees,id'];
        }

        $request->validate($rules);

        $employeeId = null;
        $canSeeData = true;

        if ($request->user()->isAdmin()) {
            $employeeId = $request->filled('employee_id') ? (int) $request->query('employee_id') : null;
        } else {
            $employeeId = $request->user()->employee?->id;
            $canSeeData = $employeeId !== null;
        }

        return [
            'canSeeData' => $canSeeData,
            'employeeId' => $employeeId,
            'fromDate' => $this->parseFilterDate($request->query('from')),
            'status' => $request->query('status'),
            'toDate' => $this->parseFilterDate($request->query('to')),
        ];
    }

    private function reportAttendanceQuery(array $filters)
    {
        return Attendance::with(['employee.position', 'employee.shift'])
            ->when(! $filters['canSeeData'], fn ($query) => $query->whereRaw('1 = 0'))
            ->whereDate('date', '<=', now()->toDateString())
            ->when($filters['employeeId'], fn ($query, $employeeId) => $query->where('employee_id', $employeeId))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['fromDate'], fn ($query, $fromDate) => $query->whereDate('date', '>=', $fromDate))
            ->when($filters['toDate'], fn ($query, $toDate) => $query->whereDate('date', '<=', $toDate))
            ->orderByDesc('date')
            ->orderBy('employee_id')
            ->orderBy('id');
    }

    public function destroy(Attendance $attendance)
    {
        if ($attendance->selfie_photo) {
            Storage::disk('public')->delete($attendance->selfie_photo);
        }

        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }
}
