<div class="col-lg-3 mb-4">
    <div class="card p-3 shadow-sm bg-surface">
        <h5 class="mb-3 text-white">Menu Cepat</h5>
        <div class="list-group list-group-flush">
            <a href="{{ route('dashboard') }}"
                class="list-group-item list-group-item-action bg-transparent text-white">Dashboard</a>
            <a href="{{ route('profile.edit') }}"
                class="list-group-item list-group-item-action bg-transparent text-white">Profil Saya</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('employees.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">Data Karyawan</a>
                <a href="{{ route('positions.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">Data Jabatan</a>
                <a href="{{ route('shifts.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">Data Shift</a>
                <a href="{{ route('reports.attendance') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">Rekap Absensi</a>
                <a href="{{ route('leave-requests.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">Izin / Sakit</a>
            @else
                <a href="{{ route('attendance.history') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">Riwayat Absensi</a>
                <a href="{{ route('leave-requests.index') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">Ajukan Izin</a>
                <a href="{{ route('attendance.qr') }}"
                    class="list-group-item list-group-item-action bg-transparent text-white">QR Code Absensi</a>
            @endif
        </div>
    </div>
</div>
