@extends('layouts.app')

@section('title', 'Pengajuan Izin')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-1">Daftar Pengajuan Izin</h2>
                <p class="text-muted">Kelola status permintaan cuti dan izin.</p>
            </div>
            @unless (auth()->user()->isAdmin())
                <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">Ajukan Izin</a>
            @endunless
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
                    @forelse($leaveRequests as $leaveRequest)
                        <tr>
                            <td>{{ $leaveRequest->employee->name }}</td>
                            <td>{{ $leaveRequest->start_date->translatedFormat('l, j F Y') }} - {{ $leaveRequest->end_date->translatedFormat('l, j F Y') }}</td>
                            <td>{{ $leaveRequest->type }}</td>
                            <td>
                                @php
                                    $badge = match ($leaveRequest->status) {
                                        'Disetujui' => 'bg-success',
                                        'Ditolak' => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $leaveRequest->status }}</span>
                            </td>
                            <td>{{ $leaveRequest->reason }}</td>
                            <td>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('leave-requests.edit', $leaveRequest) }}"
                                        class="btn btn-sm btn-outline-light">Ubah / ACC</a>
                                @else
                                    -
                                @endif
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
