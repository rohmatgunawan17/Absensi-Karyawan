@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">Laporan Absensi</h2>
                    <p class="text-muted">Ekspor dan filter rekap absensi seluruh karyawan.</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('reports.attendance.pdf', request()->query()) }}" class="btn btn-outline-light">Export
                        PDF</a>
                    <a href="{{ route('reports.attendance.excel', request()->query()) }}" class="btn btn-outline-light">Export
                        Excel</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <form class="row gy-3 gx-3" method="GET" action="{{ route('reports.attendance') }}">
            <div class="col-md-3">
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach (\App\Models\Attendance::STATUSES as $item)
                        <option value="{{ $item }}" {{ $status === $item ? 'selected' : '' }}>{{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button class="btn btn-primary">Terapkan</button>
            </div>
        </form>
    </div>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Karyawan</th>
                        <th>Status</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date->format('d M Y') }}</td>
                            <td>{{ $attendance->employee->name }}</td>
                            <td>{{ $attendance->status }}</td>
                            <td>{{ optional($attendance->check_in)->format('H:i:s') }}</td>
                            <td>{{ optional($attendance->check_out)->format('H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $attendances->links() }}</div>
    </div>
@endsection
