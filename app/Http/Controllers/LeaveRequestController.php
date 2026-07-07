<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee.position');

        if ($request->user()->isAdmin()) {
            $leaveRequests = $query->orderByDesc('start_date')->paginate(12)->withQueryString();
        } else {
            $leaveRequests = $query->where('employee_id', $request->user()->employee?->id)
                ->orderByDesc('start_date')
                ->paginate(12)
                ->withQueryString();
        }

        return view('leave_requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        if (auth()->user()->isAdmin()) {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'Admin hanya dapat menyetujui atau menolak pengajuan izin karyawan.');
        }

        return view('leave_requests.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->isAdmin()) {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'Admin hanya dapat menyetujui atau menolak pengajuan izin karyawan.');
        }

        $employee = $request->user()->employee;

        if (! $employee) {
            return redirect()
                ->route('leave-requests.index')
                ->with('error', 'Akun Anda belum terhubung dengan data karyawan.');
        }

        $request->validate([
            'type' => 'required|in:Izin,Sakit,Cuti',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y',
            'reason' => 'required|string|max:500',
        ]);

        $startDate = $this->parseDisplayDate($request->start_date);
        $endDate = $this->parseDisplayDate($request->end_date);

        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'Tanggal sampai harus sama atau setelah tanggal mulai.',
            ]);
        }

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => $request->type,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Permintaan izin berhasil diajukan.');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load('employee.position');

        return view('leave_requests.edit', compact('leaveRequest'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $data = $request->validate([
            'type' => 'required|in:Izin,Sakit,Cuti',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y',
            'reason' => 'required|string|max:500',
            'status' => 'required|in:Pending,Disetujui,Ditolak',
        ]);

        $startDate = $this->parseDisplayDate($data['start_date']);
        $endDate = $this->parseDisplayDate($data['end_date']);

        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'Tanggal sampai harus sama atau setelah tanggal mulai.',
            ]);
        }

        $data['start_date'] = $startDate->toDateString();
        $data['end_date'] = $endDate->toDateString();

        DB::transaction(function () use ($data, $leaveRequest): void {
            $this->deleteGeneratedAttendances($leaveRequest);

            $leaveRequest->update($data);

            if ($leaveRequest->status === 'Disetujui') {
                $this->syncApprovedAttendances($leaveRequest);
            }
        });

        return redirect()->route('leave-requests.index')->with('success', 'Status izin berhasil diperbarui.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        DB::transaction(function () use ($leaveRequest): void {
            $this->deleteGeneratedAttendances($leaveRequest);
            $leaveRequest->delete();
        });

        return back()->with('success', 'Permintaan izin berhasil dihapus.');
    }

    private function syncApprovedAttendances(LeaveRequest $leaveRequest): void
    {
        $status = $leaveRequest->type === 'Sakit' ? 'Sakit' : 'Izin';
        $note = Str::limit($this->attendanceNote($leaveRequest), 250, '...');

        foreach (CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date) as $date) {
            $attendance = Attendance::firstOrNew([
                'employee_id' => $leaveRequest->employee_id,
                'date' => $date->toDateString(),
            ]);

            if (
                $attendance->exists
                && ($attendance->check_in || $attendance->check_out || $attendance->selfie_photo || $attendance->status === 'Libur')
            ) {
                continue;
            }

            $attendance->fill([
                'check_in' => null,
                'check_out' => null,
                'latitude' => null,
                'longitude' => null,
                'note' => $note,
                'status' => $status,
            ])->save();
        }
    }

    private function deleteGeneratedAttendances(LeaveRequest $leaveRequest): void
    {
        Attendance::where('employee_id', $leaveRequest->employee_id)
            ->where('note', 'like', $this->attendanceNotePrefix($leaveRequest).'%')
            ->whereNull('check_in')
            ->whereNull('check_out')
            ->whereNull('selfie_photo')
            ->whereIn('status', ['Izin', 'Sakit'])
            ->delete();
    }

    private function attendanceNote(LeaveRequest $leaveRequest): string
    {
        return $this->attendanceNotePrefix($leaveRequest).' '.$leaveRequest->type.' disetujui admin: '.$leaveRequest->reason;
    }

    private function attendanceNotePrefix(LeaveRequest $leaveRequest): string
    {
        return '[Pengajuan izin #'.$leaveRequest->id.']';
    }

    private function parseDisplayDate(string $date): Carbon
    {
        return Carbon::createFromFormat('d/m/Y', $date)->startOfDay();
    }
}
