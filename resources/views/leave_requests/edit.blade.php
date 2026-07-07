@extends('layouts.app')

@section('title', 'Edit Pengajuan Izin')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <h2 class="page-title mb-1">Ubah Pengajuan Izin</h2>
                <p class="text-muted mb-3">
                    {{ $leaveRequest->employee->name }}
                    @if ($leaveRequest->employee->position)
                        · {{ $leaveRequest->employee->position->name }}
                    @endif
                </p>
                @include('partials.alerts')
                <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Tipe Izin</label>
                        <select name="type" class="form-select" required>
                            @foreach (['Cuti', 'Sakit', 'Izin'] as $type)
                                <option value="{{ $type }}"
                                    {{ old('type', $leaveRequest->type) === $type ? 'selected' : '' }}>{{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Dari</label>
                            <input type="text" name="start_date" class="form-control date-filter"
                                value="{{ old('start_date', $leaveRequest->start_date->format('d/m/Y')) }}"
                                inputmode="numeric" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sampai</label>
                            <input type="text" name="end_date" class="form-control date-filter"
                                value="{{ old('end_date', $leaveRequest->end_date->format('d/m/Y')) }}"
                                inputmode="numeric" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="4" required>{{ old('reason', $leaveRequest->reason) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Status Persetujuan</label>
                        <select name="status" class="form-select" required>
                            @foreach (['Pending', 'Disetujui', 'Ditolak'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $leaveRequest->status) === $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">
                            Jika disetujui, data absensi otomatis dibuat sesuai rentang tanggal.
                        </div>
                    </div>
                    <button class="btn btn-primary">Perbarui</button>
                    <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
