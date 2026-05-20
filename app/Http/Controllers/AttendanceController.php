<?php

namespace App\Http\Controllers;

use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');

        $attendances = Attendance::with(['employee.position', 'employee.shift'])
            ->when($search, fn($query) => $query->whereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%")))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($from, fn($query) => $query->whereDate('date', '>=', $from))
            ->when($to, fn($query) => $query->whereDate('date', '<=', $to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('attendances.index', compact('attendances', 'search', 'status', 'from', 'to'));
    }

    public function history(Request $request)
    {
        $employee = $request->user()->employee;
        $attendances = Attendance::with(['employee.position', 'employee.shift'])
            ->where('employee_id', $employee->id)
            ->when($request->query('from'), fn($query, $from) => $query->whereDate('date', '>=', $from))
            ->when($request->query('to'), fn($query, $to) => $query->whereDate('date', '<=', $to))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('attendances.history', compact('attendances'));
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
            'selfie_photo' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'note' => 'nullable|string|max:255',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpha',
        ]);

        $data = array_merge([
            'latitude' => null,
            'longitude' => null,
            'note' => null,
        ], $data);

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => now()->toDateString()],
            [
                'status' => $data['status'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'note' => $data['note'],
                'check_in' => now(),
            ]
        );

        if ($attendance->check_in && $attendance->wasRecentlyCreated === false) {
            return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
        }

        $attendance->update([
            'selfie_photo' => $request->file('selfie_photo')->store('attendances', 'public'),
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'note' => $data['note'],
            'status' => $data['status'],
            'check_in' => now(),
        ]);

        return back()->with('success', 'Absensi masuk berhasil.');
    }

    public function checkOut(Request $request)
    {
        $employee = $request->user()->employee;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (! $attendance) {
            return back()->with('error', 'Absensi masuk belum ditemukan untuk hari ini.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Anda sudah melakukan absensi pulang hari ini.');
        }

        $attendance->update(['check_out' => now()]);

        return back()->with('success', 'Absensi pulang berhasil.');
    }

    public function report(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $status = $request->query('status');

        $attendances = Attendance::with(['employee.position', 'employee.shift'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($from, fn($query) => $query->whereDate('date', '>=', $from))
            ->when($to, fn($query) => $query->whereDate('date', '<=', $to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('attendances.report', compact('attendances', 'from', 'to', 'status'));
    }

    public function exportPdf(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $attendances = Attendance::with(['employee.position', 'employee.shift'])
            ->when($from, fn($query) => $query->whereDate('date', '>=', $from))
            ->when($to, fn($query) => $query->whereDate('date', '<=', $to))
            ->get();

        $pdf = Pdf::loadView('exports.attendance-pdf', compact('attendances', 'from', 'to'));

        return $pdf->download('rekap-absensi-' . now()->format('Ymd') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new AttendancesExport($request->query('from'), $request->query('to')), 'rekap-absensi-' . now()->format('Ymd') . '.xlsx');
    }

    public function destroy(Attendance $attendance)
    {
        Storage::disk('public')->delete($attendance->selfie_photo);
        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }
}
