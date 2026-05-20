@extends('layouts.app')

@section('title', 'Pengajuan Izin')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-1">Daftar Pengajuan Izin</h2>
                <p class="text-muted">Kelola status permintaan cuti dan izin.</p>
            </div>
            <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">Ajukan Izin</a>
        </div>
    </div>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $request)
                        <tr>
                            <td>{{ $request->employee->name }}</td>
                            <td>{{ $request->start_date->format('d M Y') }} - {{ $request->end_date->format('d M Y') }}</td>
                            <td>{{ $request->type }}</td>
                            <td>{{ $request->status }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>
                                <a href="{{ route('leave-requests.edit', $request) }}"
                                    class="btn btn-sm btn-outline-light">Ubah</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pengajuan izin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $leaveRequests->links() }}</div>
    </div>
@endsection
