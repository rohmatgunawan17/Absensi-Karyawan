@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title mb-1">Dashboard Admin</h2>
                        <p class="text-muted">Ringkasan statistik absensi dan aktivitas terbaru.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card p-4">
                <h5 class="mb-3">Aksi Cepat Admin</h5>
                <div class="row g-2">
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('employees.index') }}" class="btn btn-primary w-100">Data Karyawan</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('positions.index') }}" class="btn btn-primary w-100">Data Jabatan</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('shifts.index') }}" class="btn btn-primary w-100">Data Shift</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('attendances.index') }}" class="btn btn-outline-light w-100">Data Absensi</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-light w-100">Hari Libur</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('reports.attendance') }}" class="btn btn-outline-light w-100">Rekap Absensi</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-light w-100">Izin / Sakit</a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-light w-100">Profil Saya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-6 col-xl">
            <div class="card p-4">
                <h5>Total Karyawan</h5>
                <p class="display-6 mb-0">{{ $totalEmployees }}</p>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card p-4">
                <h5>Hadir Hari Ini</h5>
                <p class="display-6 mb-0">{{ $presentToday }}</p>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card p-4">
                <h5>Izin Hari Ini</h5>
                <p class="display-6 mb-0">{{ $leaveToday }}</p>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card p-4">
                <h5>Sakit Hari Ini</h5>
                <p class="display-6 mb-0">{{ $sickToday }}</p>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card p-4">
                <h5>Libur Hari Ini</h5>
                <p class="display-6 mb-0">{{ $holidayToday }}</p>
            </div>
        </div>
    </div>
    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <div class="card p-4">
                <h5 class="mb-3">Grafik Absensi Bulanan</h5>
                <canvas id="attendanceChart" height="160"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4">
                <h5 class="mb-3">Aktivitas Terbaru</h5>
                <ul class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                        <li class="list-group-item bg-transparent text-white border-light border-opacity-10">
                            <strong>{{ $activity->employee->name }}</strong> melakukan absensi <span
                                class="badge bg-danger">{{ $activity->status }}</span>
                            <div class="text-muted small">{{ $activity->date->translatedFormat('l, j F Y') }}
                                {{ optional($activity->check_in)->format('H:i:s') }}</div>
                        </li>
                    @empty
                        <li class="list-group-item bg-transparent text-white">Belum ada aktivitas terbaru.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
        <script>
            const labels = @json($monthlyData->pluck('label'));
            const values = @json($monthlyData->pluck('count'));
            const ctx = document.getElementById('attendanceChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Total Absensi',
                            data: values,
                            borderColor: '#ff4d4d',
                            backgroundColor: 'rgba(255,77,77,0.2)',
                            fill: true,
                            tension: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#f8f9fa'
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.08)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#f8f9fa'
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.08)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#f8f9fa'
                                }
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
@endsection
