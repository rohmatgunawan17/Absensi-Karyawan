@extends('layouts.app')

@section('title', 'Hasil Scan QR')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 text-center">
                @if ($status === 'success')
                    <div class="alert alert-success">{{ $message }}</div>
                    @if (isset($attendance))
                        <p>Masuk: {{ optional($attendance->check_in)->format('H:i:s') }}</p>
                        <p>Pulang: {{ optional($attendance->check_out)->format('H:i:s') ?? 'Belum pulang' }}</p>
                    @endif
                @elseif($status === 'info')
                    <div class="alert alert-info">{{ $message }}</div>
                @else
                    <div class="alert alert-danger">{{ $message }}</div>
                @endif
                <a href="{{ url('/') }}" class="btn btn-primary mt-3">Kembali</a>
            </div>
        </div>
    </div>
@endsection
