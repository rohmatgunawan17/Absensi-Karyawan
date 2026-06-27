<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use App\Services\AttendanceHolidayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $employees = Employee::with(['user', 'position', 'shift'])
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhereHas('position', fn ($q) => $q->where('name', 'like', "%{$search}%")))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search'));
    }

    public function create()
    {
        $positions = Position::orderBy('name')->get();
        $shifts = Shift::orderBy('start_time')->get();

        return view('employees.create', compact('positions', 'shifts'));
    }

    public function store(Request $request, AttendanceHolidayService $holidayService)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'nip' => 'required|string|unique:employees,nip',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
            'shift_id' => 'required|exists:shifts,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'karyawan',
        ]);

        $employeeData = [
            'user_id' => $user->id,
            'nip' => $data['nip'],
            'name' => $data['name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'position_id' => $data['position_id'],
            'shift_id' => $data['shift_id'],
        ];

        if ($request->hasFile('photo')) {
            $employeeData['photo'] = $request->file('photo')->store('employees', 'public');
        }

        $employee = Employee::create($employeeData);
        $holidayService->syncEmployeeYear($employee, now()->year);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $positions = Position::orderBy('name')->get();
        $shifts = Shift::orderBy('start_time')->get();

        return view('employees.edit', compact('employee', 'positions', 'shifts'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$employee->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'nip' => 'required|string|unique:employees,nip,'.$employee->id,
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
            'shift_id' => 'required|exists:shifts,id',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $employee->update([
            'nip' => $data['nip'],
            'name' => $data['name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'position_id' => $data['position_id'],
            'shift_id' => $data['shift_id'],
        ]);

        if ($request->filled('password')) {
            $employee->user->update(['password' => Hash::make($data['password'])]);
        }

        $employee->user->update(['name' => $data['name'], 'email' => $data['email']]);

        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($employee->photo);
            $employee->update(['photo' => $request->file('photo')->store('employees', 'public')]);
        }

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        Storage::disk('public')->delete($employee->photo);
        $employee->user->delete();
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
