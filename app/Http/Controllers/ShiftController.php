<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $shifts = Shift::when($search, fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('start_time')
            ->paginate(12)
            ->withQueryString();

        return view('shifts.index', compact('shifts', 'search'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => ['required', 'regex:/^([01]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/'],
            'end_time' => ['required', 'regex:/^([01]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/', 'after:start_time'],
        ]);

        Shift::create($data);

        return redirect()->route('shifts.index')->with('success', 'Shift kerja berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => ['required', 'regex:/^([01]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/'],
            'end_time' => ['required', 'regex:/^([01]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/', 'after:start_time'],
        ]);

        $shift->update($data);

        return redirect()->route('shifts.index')->with('success', 'Shift kerja berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Shift kerja berhasil dihapus.');
    }
}
