@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4 shadow-sm">
                <h3 class="mb-3">Masuk</h3>
                @include('partials.alerts')
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required
                            autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label text-muted" for="remember">Ingat saya</label>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100">Masuk</button>
                </form>
                <p class="mt-3 text-center text-muted">Belum punya akun? <a href="{{ route('register') }}">Daftar
                        sekarang</a></p>
            </div>
        </div>
    </div>
@endsection
