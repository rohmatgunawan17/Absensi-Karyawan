<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    public function index(Request $request)
    {
        return Employee::with(['position', 'shift'])->paginate(20);
    }

    public function show(Employee $employee)
    {
        return $employee->load(['position', 'shift']);
    }
}
