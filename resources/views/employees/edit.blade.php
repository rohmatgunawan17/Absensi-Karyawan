@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4">
                <h2 class="page-title mb-3">Edit Karyawan</h2>
                @include('partials.alerts')
                <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $employee->user->email) }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $employee->nip) }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select" required>
                                <option value="Laki-laki" {{ $employee->gender === 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ $employee->gender === 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <select name="position_id" class="form-select" required>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}"
                                        {{ $employee->position_id === $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shift Kerja</label>
                            <select name="shift_id" class="form-select" required>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}"
                                        {{ $employee->shift_id === $shift->id ? 'selected' : '' }}>{{ $shift->name }}
                                        ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="photo" class="form-control">
                            @if ($employee->photo)
                                <small class="text-muted">Foto saat ini: <a
                                        href="{{ asset('storage/' . $employee->photo) }}" target="_blank">Lihat</a></small>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
