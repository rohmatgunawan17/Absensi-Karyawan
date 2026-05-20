<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $positions = Position::when($search, fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('positions.index', compact('positions', 'search'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'allowance' => 'required|numeric|min:0',
        ]);

        Position::create($data);

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'allowance' => 'required|numeric|min:0',
        ]);

        $position->update($data);

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
