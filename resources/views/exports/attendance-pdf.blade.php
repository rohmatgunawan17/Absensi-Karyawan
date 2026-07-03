<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
        }

        h1 {
            text-align: center;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #222;
            color: #fff;
        }

        .small {
            font-size: 0.9rem;
            color: #555;
        }

        .header {
            margin-bottom: 1rem;
            text-align: center;
        }

        .logo {
            width: 140px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img class="logo" src="{{ public_path('logo-elangomega.png') }}" alt="Elang Omega">
        <h1>Laporan Absensi Karyawan Elang Omega</h1>
        <p class="small">Periode:
            {{ $from ? \Carbon\Carbon::parse($from)->translatedFormat('l, j F Y') : 'Awal data' }} sampai
            {{ $to ? \Carbon\Carbon::parse($to)->translatedFormat('l, j F Y') : 'Data terbaru' }}
        </p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Status</th>
                <th>Masuk</th>
                <th>Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->date->translatedFormat('l, j F Y') }}</td>
                    <td>{{ $attendance->employee->name }}</td>
                    <td>{{ $attendance->status }}</td>
                    <td>{{ optional($attendance->check_in)->format('H:i:s') }}</td>
                    <td>{{ optional($attendance->check_out)->format('H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="small">Total baris: {{ $attendances->count() }}</p>
</body>

</html>
