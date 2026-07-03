<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Absensi Karyawan Elang Omega dan kalender hari libur Indonesia.">
    <meta name="theme-color" content="#b91c1c">
    <title>{{ config('app.name', 'Absensi Karyawan') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-elangomega.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo-elangomega.png') }}">
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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --red: #e23131;
            --red-dark: #b91c1c;
            --amber: #d97706;
            --surface: rgba(17, 17, 19, .92);
            --border: rgba(255, 255, 255, .1);
            --muted: #b9bbc2;
        }

        html[data-theme="light"] {
            color-scheme: light;
            --surface: rgba(255, 255, 255, .92);
            --border: rgba(15, 23, 42, .12);
            --muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: #f8fafc;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 12% 0%, rgba(226, 49, 49, .35), transparent 32rem),
                linear-gradient(145deg, #080808, #161719 55%, #090909);
            transition: color .4s ease, background .4s ease;
        }

        html[data-theme="light"] body {
            color: #172033;
            background:
                radial-gradient(circle at 12% 0%, rgba(239, 68, 68, .2), transparent 32rem),
                linear-gradient(145deg, #fff, #f8fafc 55%, #eef2f7);
        }

        a { color: inherit; }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid var(--border);
            background: rgba(9, 9, 10, .84);
            backdrop-filter: blur(18px);
            transition: background-color .4s ease, border-color .4s ease;
        }

        html[data-theme="light"] .site-header { background: rgba(255, 255, 255, .86); }

        .nav,
        .page {
            width: min(1180px, calc(100% - 32px));
            margin-inline: auto;
        }

        .nav {
            display: flex;
            min-height: 68px;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 750;
            text-decoration: none;
        }

        .brand-mark {
            display: grid;
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(220, 38, 38, .3);
            place-items: center;
        }

        .brand-mark img { width: 100%; height: 100%; padding: 4px; object-fit: contain; }

        .nav-actions { display: flex; gap: 10px; }

        .theme-toggle {
            position: relative;
            display: inline-grid;
            width: 40px;
            height: 40px;
            padding: 0;
            color: inherit;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 50%;
            place-items: center;
            overflow: hidden;
            cursor: pointer;
            transition: background-color .3s ease, transform .4s ease;
        }

        .theme-toggle:hover { background: rgba(127, 127, 127, .12); transform: rotate(8deg); }
        .theme-icon { position: absolute; width: 18px; height: 18px; transition: opacity .3s ease, transform .45s cubic-bezier(.2, .8, .2, 1); }
        .theme-icon-moon { opacity: 0; transform: rotate(-90deg) scale(.55); }
        html[data-theme="light"] .theme-icon-sun { opacity: 0; transform: rotate(90deg) scale(.55); }
        html[data-theme="light"] .theme-icon-moon { opacity: 1; transform: rotate(0) scale(1); }

        .btn {
            display: inline-flex;
            min-height: 40px;
            padding: 0 18px;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: .92rem;
            font-weight: 700;
            text-decoration: none;
            transition: .2s ease;
        }

        .btn:hover { transform: translateY(-1px); }
        .btn-secondary:hover { background: rgba(255, 255, 255, .08); }
        .btn-primary { border-color: var(--red); background: var(--red); }
        .btn-primary:hover { border-color: var(--red-dark); background: var(--red-dark); }

        .page { padding: 64px 0 48px; }

        .hero {
            max-width: 770px;
            margin-bottom: 34px;
        }

        .eyebrow {
            margin: 0 0 12px;
            color: #fca5a5;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        h1 {
            max-width: 720px;
            margin: 0;
            font-size: clamp(2.15rem, 5vw, 4.25rem);
            line-height: 1.05;
            letter-spacing: -.045em;
        }

        .hero-copy {
            max-width: 650px;
            margin: 20px 0 0;
            color: var(--muted);
            font-size: clamp(1rem, 2vw, 1.12rem);
            line-height: 1.7;
        }

        .calendar-card {
            padding: clamp(16px, 3vw, 28px);
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: var(--surface);
            box-shadow: 0 28px 70px rgba(0, 0, 0, .38);
            transition: color .4s ease, background-color .4s ease, border-color .4s ease, box-shadow .4s ease;
        }

        html[data-theme="light"] .calendar-card { box-shadow: 0 24px 60px rgba(15, 23, 42, .12); }

        .calendar-meta {
            display: flex;
            margin-bottom: 22px;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }

        .calendar-meta h2 { margin: 0 0 6px; font-size: 1.25rem; }
        .calendar-meta p { margin: 0; color: var(--muted); font-size: .9rem; }

        .legend { display: flex; flex-wrap: wrap; gap: 12px; }
        .legend-item { display: inline-flex; align-items: center; gap: 7px; color: #d8d9dd; font-size: .84rem; }
        html[data-theme="light"] .legend-item { color: #475569; }
        .legend-dot { width: 9px; height: 9px; border-radius: 50%; }
        .legend-dot.national { background: #dc2626; }
        .legend-dot.collective { background: var(--amber); }

        .fc { --fc-border-color: rgba(255, 255, 255, .1); --fc-page-bg-color: transparent; --fc-neutral-bg-color: rgba(255, 255, 255, .035); --fc-today-bg-color: rgba(226, 49, 49, .12); }
        .fc .fc-toolbar { gap: 14px; }
        .fc .fc-toolbar-title { font-size: clamp(1.1rem, 3vw, 1.55rem); text-transform: capitalize; }
        .fc .fc-button-primary { border-color: #38383c; background: #29292d; box-shadow: none !important; text-transform: capitalize; }
        .fc .fc-button-primary:hover,
        .fc .fc-button-primary:not(:disabled).fc-button-active { border-color: var(--red); background: var(--red); }
        .fc .fc-daygrid-day-number { padding: 8px; color: #ececf0; text-decoration: none; }
        .fc .fc-col-header-cell-cushion { padding: 10px 4px; color: #c9cbd1; font-size: .8rem; text-decoration: none; text-transform: uppercase; }
        .fc .fc-day-sun .fc-daygrid-day-number,
        .fc .fc-day-sun .fc-col-header-cell-cushion,
        .fc .is-national-holiday .fc-daygrid-day-number {
            color: #ff5b5b;
            font-weight: 800;
        }
        .fc .is-national-holiday { background: rgba(220, 38, 38, .08); }
        .fc .fc-event { padding: 2px 4px; border-radius: 5px; cursor: pointer; font-size: .76rem; }
        .fc .fc-daygrid-more-link { color: #fca5a5; }

        html[data-theme="light"] .fc {
            --fc-border-color: #e2e8f0;
            --fc-neutral-bg-color: #f8fafc;
            --fc-today-bg-color: rgba(220, 38, 38, .08);
            color: #1f2937;
        }
        html[data-theme="light"] .fc .fc-button-primary { color: #334155; border-color: #cbd5e1; background: #fff; }
        html[data-theme="light"] .fc .fc-daygrid-day-number { color: #334155; }
        html[data-theme="light"] .fc .fc-col-header-cell-cushion { color: #475569; }
        html[data-theme="light"] .fc .fc-day-sun .fc-daygrid-day-number,
        html[data-theme="light"] .fc .fc-day-sun .fc-col-header-cell-cushion,
        html[data-theme="light"] .fc .is-national-holiday .fc-daygrid-day-number { color: #dc2626; }

        .source-note {
            margin: 16px 2px 0;
            color: #9fa1a8;
            font-size: .78rem;
            line-height: 1.6;
        }

        .source-note a { color: #fca5a5; text-underline-offset: 3px; }

        @media (max-width: 700px) {
            .page { padding-top: 42px; }
            .nav { min-height: 62px; }
            .brand span:last-child { display: none; }
            .btn { min-height: 38px; padding-inline: 14px; }
            .calendar-meta { flex-direction: column; }
            .fc .fc-toolbar { flex-direction: column; }
            .fc .fc-toolbar-chunk { display: flex; justify-content: center; }
            .fc .fc-daygrid-event { white-space: normal; }
            .fc .fc-daygrid-day-number { padding: 5px; font-size: .78rem; }
            .fc .fc-col-header-cell-cushion { padding: 7px 2px; font-size: .67rem; }
            .fc .fc-event { padding: 1px 2px; font-size: .65rem; line-height: 1.25; }
            .fc .fc-daygrid-day-frame { min-height: 58px; }
        }
    </style>
</head>

<body>
    <header class="site-header">
        <nav class="nav" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true"><img src="{{ asset('logo-elangomega.png') }}" alt=""></span>
                <span>Absensi Karyawan Elang Omega</span>
            </a>
            <div class="nav-actions">
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
                @auth
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="btn btn-secondary" href="{{ route('login') }}">Masuk</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Daftar</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="page">
        <section class="hero">
            <p class="eyebrow">Kalender resmi Indonesia</p>
            <h1>Absensi Karyawan Elang Omega.</h1>
            <p class="hero-copy">Pantau hari libur nasional dan cuti bersama Indonesia untuk tahun berjalan maupun tahun berikutnya.</p>
        </section>

        <section class="calendar-card" aria-labelledby="calendar-title">
            <div class="calendar-meta">
                <div>
                    <h2 id="calendar-title">Kalender Libur Indonesia</h2>
                    <p>Klik agenda untuk melihat jenis hari libur.</p>
                </div>
                <div class="legend" aria-label="Keterangan warna">
                    <span class="legend-item"><span class="legend-dot national"></span>Libur Nasional</span>
                    <span class="legend-item"><span class="legend-dot collective"></span>Cuti Bersama</span>
                </div>
            </div>
            <div id="calendar"></div>
            <p class="source-note">Sumber: SKB 3 Menteri Tahun 2025 tentang Hari Libur Nasional dan Cuti Bersama Tahun 2026, dipublikasikan oleh <a href="{{ config('indonesian_holidays')[2026]['source'] }}" target="_blank" rel="noopener noreferrer">Kementerian Sekretariat Negara</a>.</p>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'id',
                firstDay: 1,
                height: 'auto',
                dayMaxEvents: 3,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                buttonText: { today: 'Hari ini' },
                events: function (fetchInfo, successCallback, failureCallback) {
                    const middleDate = new Date((fetchInfo.start.getTime() + fetchInfo.end.getTime()) / 2);
                    fetch(`{{ route('holidays.index') }}?year=${middleDate.getFullYear()}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal memuat kalender');
                            return response.json();
                        })
                        .then(successCallback)
                        .catch(failureCallback);
                },
                eventClick: function (info) {
                    const type = info.event.extendedProps.type || 'Hari Libur';
                    alert(`${type}\n${info.event.title}`);
                },
                eventDidMount: function (info) {
                    if (info.event.extendedProps.type === 'Libur Nasional') {
                        info.el.closest('.fc-daygrid-day')?.classList.add('is-national-holiday');
                    }
                }
            });

            calendar.render();
        });
    </script>
</body>

</html>
