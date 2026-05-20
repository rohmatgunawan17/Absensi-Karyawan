@extends('layouts.app')

@section('title', 'Data Jabatan')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">Data Jabatan</h2>
                    <p class="text-muted">Atur jabatan dan tunjangan karyawan.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <form class="input-group" method="GET" action="{{ route('positions.index') }}">
                <input type="search" name="search" class="form-control" placeholder="Cari jabatan"
                    value="{{ $search }}">
                <button class="btn btn-outline-light">Cari</button>
            </form>
        </div>
        <div class="col-md-8 text-end">
            <a href="{{ route('positions.create') }}" class="btn btn-primary">Tambah Jabatan</a>
        </div>
    </div>
    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama Jabatan</th>
                        <th>Tunjangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                        <tr>
                            <td>{{ $position->name }}</td>
                            <td>Rp {{ number_format($position->allowance, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('positions.edit', $position) }}"
                                    class="btn btn-sm btn-outline-light">Edit</a>
                                <form action="{{ route('positions.destroy', $position) }}" method="POST"
                                    class="d-inline-block" onsubmit="return confirm('Hapus jabatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data jabatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $positions->links() }}</div>
    </div>
@endsection
