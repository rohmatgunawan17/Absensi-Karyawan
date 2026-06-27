<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QRCodeController extends Controller
{
    public function show(Request $request)
    {
        $employee = $request->user()->employee;

        // Generate encrypted token per employee
        $token = $employee ? encrypt($employee->id) : null;
        $qrCodeValue = $token ? route('attendance.scan.short', ['token' => $token]) : route('dashboard');
        $qrCodeUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl='.urlencode($qrCodeValue);
        $todayAttendance = null;

        if ($employee) {
            $todayAttendance = $employee->attendances()->whereDate('date', now())->first();
        }

        return view('attendances.qr', compact('qrCodeValue', 'qrCodeUrl', 'todayAttendance'));
    }

    public function scan(Request $request, $token)
    {
        try {
            $employeeId = decrypt($token);
        } catch (\Throwable $e) {
            Log::warning('Invalid QR token scan: '.$e->getMessage());

            return view('attendances.scan-result', ['status' => 'error', 'message' => 'Token QR tidak valid.']);
        }

        $employee = Employee::find($employeeId);
        if (! $employee) {
            return view('attendances.scan-result', ['status' => 'error', 'message' => 'Karyawan tidak ditemukan.']);
        }

        $attendance = $employee->attendances()->whereDate('date', now())->first();

        if (! $attendance || ! $attendance->check_in) {
            $attendance ??= new Attendance([
                'employee_id' => $employee->id,
                'date' => now()->toDateString(),
            ]);

            $attendance->fill(['status' => 'Hadir', 'check_in' => now()])->save();

            return view('attendances.scan-result', ['status' => 'success', 'message' => 'Absensi masuk berhasil untuk '.$employee->name, 'attendance' => $attendance]);
        }

        if (! $attendance->check_out) {
            $attendance->update(['check_out' => now()]);

            return view('attendances.scan-result', ['status' => 'success', 'message' => 'Absensi pulang berhasil untuk '.$employee->name, 'attendance' => $attendance]);
        }

        return view('attendances.scan-result', ['status' => 'info', 'message' => 'Absensi sudah lengkap untuk hari ini.']);
    }
}
