@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="page-title mb-1">Edit Absensi</h2>
                        <p class="text-muted mb-0">{{ $attendance->employee->name }} · {{ $attendance->date->translatedFormat('l, j F Y') }}</p>
                    </div>
                    <a href="{{ route('attendances.index') }}" class="btn btn-outline-light">Kembali</a>
                </div>

                @include('partials.alerts')

                <form method="POST" action="{{ route('attendances.update', $attendance) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Karyawan</label>
                            <input class="form-control" value="{{ $attendance->employee->name }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input class="form-control" value="{{ $attendance->date->translatedFormat('l, j F Y') }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach (\App\Models\Attendance::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(old('status', $attendance->status) === $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jam Masuk</label>
                            <input type="time" name="check_in" class="form-control"
                                value="{{ old('check_in', $attendance->check_in?->format('H:i')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jam Pulang</label>
                            <input type="time" name="check_out" class="form-control"
                                value="{{ old('check_out', $attendance->check_out?->format('H:i')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Latitude</label>
                            <input name="latitude" class="form-control"
                                value="{{ old('latitude', $attendance->latitude) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Longitude</label>
                            <input name="longitude" class="form-control"
                                value="{{ old('longitude', $attendance->longitude) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="3">{{ old('note', $attendance->note) }}</textarea>
                        </div>
                    </div>

                    <div class="d-grid d-sm-flex gap-2 mt-4">
                        <button class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('attendances.index') }}" class="btn btn-outline-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
