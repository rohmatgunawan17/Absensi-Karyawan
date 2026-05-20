<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestApiController extends Controller
{
    public function index(Request $request)
    {
        return LeaveRequest::with('employee.position')
            ->when($request->query('status'), fn($query, $status) => $query->where('status', $status))
            ->paginate(20);
    }
}
