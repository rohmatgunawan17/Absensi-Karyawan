<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_leave_permission_and_sick_requests_into_attendances(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employeeUser = User::factory()->create(['role' => 'karyawan']);
        $employee = Employee::create([
            'name' => 'Karyawan Test',
            'nip' => 'EMP-001',
            'user_id' => $employeeUser->id,
        ]);

        $cases = [
            ['type' => 'Izin', 'date' => '2026-07-01', 'attendance_status' => 'Izin'],
            ['type' => 'Cuti', 'date' => '2026-07-02', 'attendance_status' => 'Izin'],
            ['type' => 'Sakit', 'date' => '2026-07-03', 'attendance_status' => 'Sakit'],
        ];

        foreach ($cases as $case) {
            $leaveRequest = LeaveRequest::create([
                'employee_id' => $employee->id,
                'type' => $case['type'],
                'start_date' => $case['date'],
                'end_date' => $case['date'],
                'reason' => $case['type'].' test',
                'status' => 'Pending',
            ]);

            $this->actingAs($admin)
                ->put(route('leave-requests.update', $leaveRequest), [
                    'type' => $leaveRequest->type,
                    'start_date' => $leaveRequest->start_date->format('d/m/Y'),
                    'end_date' => $leaveRequest->end_date->format('d/m/Y'),
                    'reason' => $leaveRequest->reason,
                    'status' => 'Disetujui',
                ])
                ->assertRedirect(route('leave-requests.index'));

            $this->assertDatabaseHas('leave_requests', [
                'id' => $leaveRequest->id,
                'status' => 'Disetujui',
            ]);

            $this->assertTrue(
                Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $case['date'])
                    ->where('status', $case['attendance_status'])
                    ->exists()
            );
        }
    }

    public function test_admin_rejecting_an_approved_leave_removes_generated_attendance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employeeUser = User::factory()->create(['role' => 'karyawan']);
        $employee = Employee::create([
            'name' => 'Karyawan Test',
            'nip' => 'EMP-002',
            'user_id' => $employeeUser->id,
        ]);

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => 'Izin',
            'start_date' => '2026-07-04',
            'end_date' => '2026-07-04',
            'reason' => 'Urus keluarga',
            'status' => 'Disetujui',
        ]);

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => '2026-07-04',
            'status' => 'Izin',
            'note' => '[Pengajuan izin #'.$leaveRequest->id.'] Izin disetujui admin: Urus keluarga',
        ]);

        $this->actingAs($admin)
            ->put(route('leave-requests.update', $leaveRequest), [
                'type' => $leaveRequest->type,
                'start_date' => $leaveRequest->start_date->format('d/m/Y'),
                'end_date' => $leaveRequest->end_date->format('d/m/Y'),
                'reason' => $leaveRequest->reason,
                'status' => 'Ditolak',
            ])
            ->assertRedirect(route('leave-requests.index'));

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'Ditolak',
        ]);

        $this->assertFalse(
            Attendance::where('employee_id', $employee->id)
                ->whereDate('date', '2026-07-04')
                ->where('status', 'Izin')
                ->exists()
        );
    }
}
