<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

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
        return view('leave_requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:Izin,Sakit,Cuti',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        LeaveRequest::create([
            'employee_id' => $request->user()->employee->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Permintaan izin berhasil diajukan.');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        return view('leave_requests.edit', compact('leaveRequest'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'status' => 'required|in:Pending,Disetujui,Ditolak',
        ]);

        $leaveRequest->update(['status' => $request->status]);

        return redirect()->route('leave-requests.index')->with('success', 'Status izin berhasil diperbarui.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return back()->with('success', 'Permintaan izin berhasil dihapus.');
    }
}
