<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->isAdmin()) {
            $totalEmployees = Employee::count();
            $presentToday = Attendance::whereDate('date', now())->where('status', 'Hadir')->count();
            $leaveToday = Attendance::whereDate('date', now())->where('status', 'Izin')->count();
            $sickToday = Attendance::whereDate('date', now())->where('status', 'Sakit')->count();
            $absentToday = Attendance::whereDate('date', now())->where('status', 'Alpha')->count();
            $recentActivities = Attendance::with('employee')->latest()->take(5)->get();
            $monthlyData = collect();

            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i)->format('Y-m');
                $monthlyData->push([
                    'label' => now()->subMonths($i)->format('M Y'),
                    'count' => Attendance::whereYear('date', now()->subMonths($i)->year)
                        ->whereMonth('date', now()->subMonths($i)->month)
                        ->count(),
                ]);
            }

            return view('dashboard.admin', compact(
                'totalEmployees',
                'presentToday',
                'leaveToday',
                'sickToday',
                'absentToday',
                'recentActivities',
                'monthlyData'
            ));
        }

        $employee = $request->user()->employee;
        $todayAttendance = $employee?->attendances()->whereDate('date', now())->first();
        $pendingLeave = LeaveRequest::where('employee_id', $employee->id ?? 0)
            ->where('status', 'Pending')
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.employee', compact('employee', 'todayAttendance', 'pendingLeave'));
    }
}
