<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        return view('profile.edit', compact('user', 'employee'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email']]);

        $employee?->update([
            'name' => $data['name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePhoto(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('photo')->store('employees', 'public');

        if ($employee?->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee?->update(['photo' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
