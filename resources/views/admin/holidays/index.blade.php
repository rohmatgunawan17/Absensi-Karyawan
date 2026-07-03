@extends('layouts.app')

@section('title', 'Kelola Hari Libur')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Kelola Hari Libur</h2>
            <p class="text-muted mb-0">Tambahkan libur tahun berikutnya secara manual atau melalui file CSV.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Kembali</a>
    </div>

    @include('partials.alerts')

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="mb-3">Input Manual</h5>
                <form method="POST" action="{{ route('admin.holidays.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis</label>
                        <select name="type" class="form-select" required>
                            <option value="national" @selected(old('type') === 'national')>Libur Nasional</option>
                            <option value="collective" @selected(old('type') === 'collective')>Cuti Bersama</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nama Hari Libur</label>
                        <input name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="Contoh: Tahun Baru 2027 Masehi" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Sumber / Nomor SKB <span class="text-muted">(opsional)</span></label>
                        <input name="source" class="form-control" value="{{ old('source') }}"
                            placeholder="URL atau nomor keputusan pemerintah">
                    </div>
                    <div class="col-12 d-grid d-sm-flex">
                        <button class="btn btn-primary">Simpan dan Sinkronkan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="mb-3">Impor CSV / Excel</h5>
                <p class="text-muted small">Gunakan CSV UTF-8, XLSX, atau XLS. Kolom wajib:
                    <code>tanggal,nama</code>. Kolom opsional: <code>jenis,sumber</code>.</p>
                <div class="rounded border border-secondary border-opacity-25 p-3 mb-3 small">
                    <code>tanggal,nama,jenis,sumber</code><br>
                    <code>2027-01-01,Tahun Baru 2027,national,SKB 3 Menteri</code><br>
                    <code>2027-12-24,Cuti Bersama Natal,collective,SKB 3 Menteri</code>
                </div>
                <form id="holidayImportForm" method="POST" action="{{ route('admin.holidays.import') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <label class="form-label" for="holidayFile">Pilih File CSV / Excel</label>
                    <input id="holidayFile" type="file" name="holiday_file" class="form-control"
                        accept=".csv,.xlsx,.xls,text/csv" required>
                    <input id="holidayData" type="hidden" name="holiday_data">
                    <div id="holidayFileStatus" class="form-text text-muted mb-3">Maksimal 2 MB.</div>
                    <button id="holidayImportButton" class="btn btn-primary w-100">Impor dan Generate Absensi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1">Daftar Hari Libur {{ $year }}</h5>
                <p class="text-muted small mb-0">Data ini langsung tampil pada kalender publik.</p>
            </div>
            <form method="GET" action="{{ route('admin.holidays.index') }}">
                <select name="year" class="form-select" onchange="this.form.submit()" aria-label="Pilih tahun">
                    @foreach ($years as $item)
                        <option value="{{ $item }}" @selected((int) $item === $year)>{{ $item }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Sumber</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($holidays as $holiday)
                        <tr>
                            <td>{{ $holiday->date->translatedFormat('l, j F Y') }}</td>
                            <td>{{ $holiday->name }}</td>
                            <td>
                                <span class="badge {{ $holiday->type === 'national' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $holiday->type === 'national' ? 'Libur Nasional' : 'Cuti Bersama' }}
                                </span>
                            </td>
                            <td>{{ $holiday->source ?: '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.holidays.destroy', $holiday) }}"
                                    onsubmit="return confirm('Hapus hari libur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Belum ada data hari libur untuk tahun {{ $year }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $holidays->links() }}</div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('holidayImportForm');
            const fileInput = document.getElementById('holidayFile');
            const dataInput = document.getElementById('holidayData');
            const status = document.getElementById('holidayFileStatus');
            const button = document.getElementById('holidayImportButton');
            let fileReady = false;

            fileInput?.addEventListener('change', function() {
                const file = fileInput.files?.[0];
                dataInput.value = '';
                fileReady = false;

                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    fileInput.value = '';
                    status.textContent = 'Ukuran file melebihi 2 MB.';
                    status.classList.add('text-danger');
                    return;
                }

                status.classList.remove('text-danger');
                status.textContent = 'Membaca file...';
                if (!file.name.toLowerCase().endsWith('.csv')) {
                    fileReady = true;
                    status.textContent = 'File Excel siap diimpor.';
                    return;
                }

                const reader = new FileReader();
                reader.addEventListener('load', function() {
                    dataInput.value = reader.result;
                    fileReady = true;
                    status.textContent = 'File siap diimpor.';
                });
                reader.addEventListener('error', function() {
                    status.textContent = 'File gagal dibaca. Silakan pilih ulang.';
                    status.classList.add('text-danger');
                });
                reader.readAsText(file);
            });

            form?.addEventListener('submit', function(event) {
                if (fileInput.files.length && !fileReady) {
                    event.preventDefault();
                    status.textContent = 'Tunggu sebentar, file masih dibaca.';
                    return;
                }

                if (dataInput.value) fileInput.removeAttribute('name');
                button.disabled = true;
                button.textContent = 'Mengimpor...';
            });
        });
    </script>
@endpush
