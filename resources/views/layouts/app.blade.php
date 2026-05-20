<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Absensi Karyawan') }} - @yield('title')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-pAC7Xcf5YwV/6JzT5cYjN2uU8x1ePN8J4D8u8XEe6jHtKgMuoB8lZqT1TnGcsXnF5ccK8x1pVOYkx7p3PS0w2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: radial-gradient(circle at top left, #ff3d3d, #000000 40%);
            color: #f8f9fa;
        }

        .card {
            background: rgba(10, 10, 10, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar,
        .sidebar {
            background: rgba(15, 15, 15, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        a,
        .nav-link {
            color: #f8f9fa;
        }

        .nav-link:hover {
            color: #ff6b6b;
        }

        .page-title {
            color: #ffcccc;
        }

        .btn-primary {
            background: #d82121;
            border-color: #bf1111;
        }

        .btn-primary:hover {
            background: #c11b1b;
        }

        .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.35);
        }

        .table thead {
            background: rgba(255, 255, 255, 0.05);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.08);
            color: #f8f9fa;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .form-control:focus {
            border-color: #ff4d4d;
            box-shadow: none;
        }

        .bg-surface {
            background: rgba(18, 18, 18, 0.92);
        }

        .text-muted {
            color: #d1d1d1 !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top px-3">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">Absensi Karyawan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                @auth
                    <li class="nav-item me-2"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item me-2"><a class="nav-link" href="{{ route('profile.edit') }}">Profil</a></li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-light btn-sm">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item me-2"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <div class="container-fluid py-4">
        @yield('content')
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: '{{ session('success') }}',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                    toast: true,
                    position: 'top-end',
                    timer: 4000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
