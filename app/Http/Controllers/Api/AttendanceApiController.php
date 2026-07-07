<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    public function index(Request $request)
    {
        return Attendance::with(['employee.position', 'employee.shift'])
            ->whereDate('date', '<=', now()->toDateString())
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('from'), fn ($query, $from) => $query->whereDate('date', '>=', $from))
            ->when($request->query('to'), fn ($query, $to) => $query->whereDate('date', '<=', $to))
            ->orderByDesc('date')
            ->orderBy('employee_id')
            ->orderBy('id')
            ->paginate(20);
    }
}
