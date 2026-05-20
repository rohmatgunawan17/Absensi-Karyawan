@extends('layouts.app')

@section('title', 'Ajukan Izin')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <h2 class="page-title mb-3">Form Pengajuan Izin</h2>
                @include('partials.alerts')
                <form method="POST" action="{{ route('leave-requests.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tipe Izin</label>
                        <select name="type" class="form-select" required>
                            <option value="">Pilih tipe</option>
                            @foreach (['Cuti', 'Sakit', 'Izin'] as $type)
                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Dari</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sampai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                    </div>
                    <button class="btn btn-primary">Kirim Pengajuan</button>
                    <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
