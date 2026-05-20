@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title mb-1">Dashboard Karyawan</h2>
                        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}. Kelola absensi dan lihat riwayatmu.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-4">
                <h5>Status Absensi Hari Ini</h5>
                <p class="fs-3 mb-0">{{ $todayAttendance?->status ?? 'Belum absen' }}</p>
                @if ($todayAttendance)
                    <p class="text-muted">Masuk: {{ optional($todayAttendance->check_in)->format('H:i:s') }} | Pulang:
                        {{ optional($todayAttendance->check_out)->format('H:i:s') }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h5>Aksi Cepat</h5>
                <div class="row g-2">
                    <div class="col-12">
                        <a href="{{ route('attendance.qr') }}" class="btn btn-primary w-100">Lihat QR Code Absensi</a>
                    </div>
                    @if ($todayAttendance && !$todayAttendance->check_out)
                        <div class="col-12">
                            <form method="POST" action="{{ route('attendance.checkout') }}" class="mb-0">
                                @csrf
                                <button class="btn btn-danger w-100">Absensi Pulang</button>
                            </form>
                        </div>
                    @endif
                    <div class="col-12">
                        <a href="{{ route('leave-requests.create') }}" class="btn btn-outline-light w-100">Ajukan Izin /
                            Sakit</a>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('attendance.history') }}" class="btn btn-outline-light w-100">Lihat Riwayat</a>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-light w-100">Profil Saya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <div class="card p-4">
                <h5>Profil Karyawan</h5>
                <dl class="row mb-0 text-muted">
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $employee?->name ?? '-' }}</dd>
                    <dt class="col-sm-4">NIP</dt>
                    <dd class="col-sm-8">{{ $employee?->nip ?? '-' }}</dd>
                    <dt class="col-sm-4">Jabatan</dt>
                    <dd class="col-sm-8">{{ $employee?->position?->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Shift</dt>
                    <dd class="col-sm-8">{{ $employee?->shift?->name ?? '-' }}</dd>
                </dl>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4">
                <h5>Permintaan Izin</h5>
                @forelse($pendingLeave as $leave)
                    <div class="mb-3 border-bottom border-white border-opacity-10 pb-3">
                        <strong>{{ $leave->type }}</strong>
                        <div class="text-muted">{{ $leave->start_date->format('d M Y') }} -
                            {{ $leave->end_date->format('d M Y') }}</div>
                        <div class="text-white small">Status: {{ $leave->status }}</div>
                    </div>
                @empty
                    <p class="mb-0">Tidak ada permintaan izin berjalan.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
