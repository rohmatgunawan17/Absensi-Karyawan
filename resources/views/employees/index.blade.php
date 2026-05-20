@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">Data Karyawan</h2>
                    <p class="text-muted">Kelola semua karyawan dan detail absensi mereka.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <form class="input-group" method="GET" action="{{ route('employees.index') }}">
                <input type="search" name="search" class="form-control" placeholder="Cari nama, NIP, jabatan"
                    value="{{ $search }}">
                <button class="btn btn-outline-light">Cari</button>
            </form>
        </div>
        <div class="col-md-8 text-end">
            <a href="{{ route('employees.create') }}" class="btn btn-primary">Tambah Karyawan</a>
        </div>
    </div>
    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Shift</th>
                        <th>Kontak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->nip }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->position?->name }}</td>
                            <td>{{ $employee->shift?->name }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>
                                <a href="{{ route('employees.edit', $employee) }}"
                                    class="btn btn-sm btn-outline-light">Edit</a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                                    class="d-inline-block" onsubmit="return confirm('Hapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Data karyawan tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $employees->links() }}</div>
    </div>
@endsection
