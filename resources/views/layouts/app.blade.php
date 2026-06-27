<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Absensi Karyawan') }} - @yield('title')</title>
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('app-theme');
                const preferredTheme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                document.documentElement.dataset.theme = savedTheme || preferredTheme;
            } catch (error) {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-pAC7Xcf5YwV/6JzT5cYjN2uU8x1ePN8J4D8u8XEe6jHtKgMuoB8lZqT1TnGcsXnF5ccK8x1pVOYkx7p3PS0w2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            color-scheme: dark;
        }

        html[data-theme="light"] {
            color-scheme: light;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #ff3d3d, #000000 40%);
            color: #f8f9fa;
        }

        body,
        .card,
        .navbar,
        .sidebar,
        .form-control,
        .form-select,
        .table,
        .list-group-item,
        .page-link,
        .btn-outline-light,
        .text-muted {
            transition: color .35s ease, background-color .35s ease, border-color .35s ease, box-shadow .35s ease;
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

        .form-control::placeholder {
            color: #a9a9a9;
        }

        .password-field {
            position: relative;
        }

        .password-field .form-control {
            padding-right: 3rem;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: .45rem;
            display: grid;
            width: 2.25rem;
            height: 2.25rem;
            padding: 0;
            color: #d1d5db;
            background: transparent;
            border: 0;
            border-radius: .45rem;
            place-items: center;
            transform: translateY(-50%);
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            color: #fff;
            background: rgba(255, 255, 255, .1);
            outline: none;
        }

        .password-toggle svg {
            width: 1.15rem;
            height: 1.15rem;
        }

        .pagination {
            flex-wrap: wrap;
            gap: .35rem;
            margin-bottom: 0;
        }

        .pagination .page-link {
            min-width: 2.35rem;
            color: #f3f4f6;
            text-align: center;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: .5rem !important;
        }

        .pagination .page-link:hover {
            color: #fff;
            background: rgba(216, 33, 33, .55);
            border-color: #d82121;
        }

        .pagination .page-item.active .page-link {
            color: #fff;
            background: #d82121;
            border-color: #d82121;
        }

        .pagination .page-item.disabled .page-link {
            color: #747474;
            background: rgba(255, 255, 255, .025);
            border-color: rgba(255, 255, 255, .07);
        }

        .bg-surface {
            background: rgba(18, 18, 18, 0.92);
        }

        .text-muted {
            color: #d1d1d1 !important;
        }

        .theme-toggle {
            position: relative;
            display: inline-grid;
            width: 2.5rem;
            height: 2.5rem;
            padding: 0;
            color: #f8fafc;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 50%;
            place-items: center;
            overflow: hidden;
        }

        .theme-toggle:hover {
            color: #fff;
            background: rgba(255, 255, 255, .16);
            transform: rotate(8deg);
        }

        .theme-icon {
            position: absolute;
            width: 1.15rem;
            height: 1.15rem;
            transition: opacity .3s ease, transform .45s cubic-bezier(.2, .8, .2, 1);
        }

        .theme-icon-moon {
            opacity: 0;
            transform: rotate(-90deg) scale(.55);
        }

        html[data-theme="light"] .theme-icon-sun {
            opacity: 0;
            transform: rotate(90deg) scale(.55);
        }

        html[data-theme="light"] .theme-icon-moon {
            opacity: 1;
            transform: rotate(0) scale(1);
        }

        html[data-theme="light"] body {
            color: #1f2937;
            background: radial-gradient(circle at top left, #fecaca, #f8fafc 42%, #eef2f7);
        }

        html[data-theme="light"] .card {
            color: #1f2937;
            background: rgba(255, 255, 255, .88);
            border-color: rgba(15, 23, 42, .1);
            box-shadow: 0 .5rem 1.5rem rgba(15, 23, 42, .08);
        }

        html[data-theme="light"] .navbar,
        html[data-theme="light"] .sidebar {
            background: rgba(255, 255, 255, .92);
            border-color: rgba(15, 23, 42, .1);
        }

        html[data-theme="light"] .navbar-brand,
        html[data-theme="light"] .nav-link,
        html[data-theme="light"] .text-white,
        html[data-theme="light"] .list-group-item {
            color: #1f2937 !important;
        }

        html[data-theme="light"] .nav-link:hover,
        html[data-theme="light"] a:not(.btn) {
            color: #b91c1c;
        }

        html[data-theme="light"] .page-title {
            color: #991b1b;
        }

        html[data-theme="light"] .text-muted {
            color: #64748b !important;
        }

        html[data-theme="light"] .bg-surface {
            background: rgba(255, 255, 255, .9);
        }

        html[data-theme="light"] .form-control,
        html[data-theme="light"] .form-select {
            color: #1f2937;
            background-color: rgba(255, 255, 255, .95);
            border-color: #cbd5e1;
        }

        html[data-theme="light"] .form-control::placeholder {
            color: #94a3b8;
        }

        html[data-theme="light"] .table-dark {
            --bs-table-color: #1f2937;
            --bs-table-bg: transparent;
            --bs-table-border-color: #e2e8f0;
            --bs-table-striped-color: #1f2937;
            --bs-table-hover-color: #111827;
            --bs-table-hover-bg: rgba(220, 38, 38, .06);
        }

        html[data-theme="light"] .btn-outline-light {
            color: #334155;
            border-color: #94a3b8;
        }

        html[data-theme="light"] .btn-outline-light:hover {
            color: #fff;
            background: #475569;
        }

        html[data-theme="light"] .pagination .page-link {
            color: #334155;
            background: rgba(255, 255, 255, .9);
            border-color: #cbd5e1;
        }

        html[data-theme="light"] .theme-toggle {
            color: #334155;
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        html[data-theme="light"] .navbar-toggler-icon {
            filter: invert(1);
        }

        @media (max-width: 767.98px) {
            body {
                font-size: .94rem;
            }

            .container-fluid {
                padding: 1rem !important;
                overflow-x: hidden;
            }

            .card {
                padding: 1rem !important;
                border-radius: .75rem;
            }

            .d-flex.justify-content-between.align-items-center {
                flex-wrap: wrap;
                gap: .75rem;
            }

            .btn-group {
                display: grid;
                width: 100%;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .navbar-nav {
                align-items: stretch !important;
                gap: .5rem;
                padding-top: .75rem;
            }

            .navbar-nav .nav-item,
            .navbar-nav .btn {
                width: 100%;
                margin-right: 0 !important;
                text-align: center;
            }

            .table-responsive {
                width: calc(100% + 2rem);
                margin-inline: -1rem;
                padding-inline: 1rem;
            }

            .table-responsive .table {
                min-width: 680px;
                font-size: .85rem;
            }

            .pagination {
                justify-content: center;
            }
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
                <li class="nav-item me-2 d-flex align-items-center justify-content-center">
                    <button id="themeToggle" class="theme-toggle" type="button" aria-label="Aktifkan mode siang"
                        title="Ganti mode tampilan">
                        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"></path>
                        </svg>
                        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"></path>
                        </svg>
                    </button>
                </li>
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
                    <li class="nav-item me-2"><a class="btn btn-outline-light btn-sm px-3" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm px-3" href="{{ route('register') }}">Daftar</a></li>
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
            const themeToggle = document.getElementById('themeToggle');

            function updateThemeLabel() {
                const isLight = document.documentElement.dataset.theme === 'light';
                themeToggle?.setAttribute('aria-label', isLight ? 'Aktifkan mode malam' : 'Aktifkan mode siang');
            }

            themeToggle?.addEventListener('click', function() {
                const nextTheme = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
                document.documentElement.dataset.theme = nextTheme;
                localStorage.setItem('app-theme', nextTheme);
                updateThemeLabel();
            });

            updateThemeLabel();

            const eyeOpen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>';
            const eyeClosed = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m3 3 18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 4.2A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a18.5 18.5 0 0 1-2 3.2M6.6 6.6C3.5 8.5 2 12 2 12s3.5 8 10 8a9.8 9.8 0 0 0 5.4-1.6"/></svg>';

            document.querySelectorAll('input[type="password"]').forEach(function(input, index) {
                const wrapper = document.createElement('div');
                const button = document.createElement('button');
                const inputId = input.id || 'password-field-' + index;

                input.id = inputId;
                wrapper.className = 'password-field';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);

                button.type = 'button';
                button.className = 'password-toggle';
                button.setAttribute('aria-controls', inputId);
                button.setAttribute('aria-label', 'Tampilkan password');
                button.setAttribute('aria-pressed', 'false');
                button.innerHTML = eyeOpen;
                wrapper.appendChild(button);

                button.addEventListener('click', function() {
                    const isVisible = input.type === 'text';
                    input.type = isVisible ? 'password' : 'text';
                    button.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
                    button.setAttribute('aria-pressed', String(!isVisible));
                    button.innerHTML = isVisible ? eyeOpen : eyeClosed;
                    input.focus();
                });
            });

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
