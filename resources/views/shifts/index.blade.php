@extends('layouts.app')

@section('title', 'Data Shift')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">Data Shift Kerja</h2>
                    <p class="text-muted">Buat dan atur jadwal kerja per shift.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <form class="input-group" method="GET" action="{{ route('shifts.index') }}">
                <input type="search" name="search" class="form-control" placeholder="Cari shift"
                    value="{{ $search }}">
                <button class="btn btn-outline-light">Cari</button>
            </form>
        </div>
        <div class="col-md-8 text-end">
            <a href="{{ route('shifts.create') }}" class="btn btn-primary">Tambah Shift</a>
        </div>
    </div>
    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>Shift</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                        <tr>
                            <td>{{ $shift->name }}</td>
                            <td>{{ $shift->start_time }} - {{ $shift->end_time }}</td>
                            <td>
                                <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-sm btn-outline-light">Edit</a>
                                <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="d-inline-block"
                                    onsubmit="return confirm('Hapus shift ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data shift.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $shifts->links() }}</div>
    </div>
@endsection
