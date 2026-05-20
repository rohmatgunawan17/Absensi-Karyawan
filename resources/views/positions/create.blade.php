@extends('layouts.app')

@section('title', 'Tambah Jabatan')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <h2 class="page-title mb-3">Tambah Jabatan</h2>
                @include('partials.alerts')
                <form method="POST" action="{{ route('positions.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Jabatan</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tunjangan</label>
                        <input type="number" name="allowance" class="form-control" value="{{ old('allowance') }}"
                            min="0" required>
                    </div>
                    <button class="btn btn-primary">Simpan</button>
                    <a href="{{ route('positions.index') }}" class="btn btn-outline-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
