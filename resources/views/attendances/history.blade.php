@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">Riwayat Absensi</h2>
                    <p class="text-muted">Lihat semua catatan absensi Anda.</p>
                </div>
                <a href="{{ route('attendance.qr') }}" class="btn btn-primary">Absensi dengan QR</a>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <form class="row gy-3 gx-3" method="GET" action="{{ route('attendance.history') }}">
            <div class="col-md-3">
                <input type="text" name="from" value="{{ request('from') }}" class="form-control date-filter"
                    inputmode="numeric" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}">
            </div>
            <div class="col-md-3">
                <input type="text" name="to" value="{{ request('to') }}" class="form-control date-filter"
                    inputmode="numeric" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="attendance" @selected($type === 'attendance')>Hasil Absensi</option>
                    <option value="holiday" @selected($type === 'holiday')>Hari Libur</option>
                    <option value="all" @selected($type === 'all')>Semua Data</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date->translatedFormat('l, j F Y') }}</td>
                            <td>
                                @php
                                    $badge = match ($attendance->status) {
                                        'Hadir' => 'bg-success',
                                        'Izin' => 'bg-info text-dark',
                                        'Sakit' => 'bg-warning text-dark',
                                        'Alpha', 'Libur' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $attendance->status }}</span>
                            </td>
                            <td>{{ optional($attendance->check_in)->format('H:i:s') ?? '-' }}</td>
                            <td>{{ optional($attendance->check_out)->format('H:i:s') ?? '-' }}</td>
                            <td>{{ $attendance->note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                {{ $type === 'holiday' ? 'Belum ada data hari libur.' : 'Belum ada hasil absensi pada filter ini.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $attendances->links() }}</div>
    </div>
@endsection
