@extends('layouts.app')

@section('title', 'Absensi QR')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 text-center">
                <h2 class="page-title mb-3">Absensi dengan QR Code</h2>
                <p class="text-muted mb-4">Gunakan kode QR untuk melakukan check-in dan check-out dengan cepat.</p>
                <div class="d-flex justify-content-center mb-3">
                    <div id="qrCodeContainer"></div>
                </div>
                <button id="downloadQrBtn" class="btn btn-success mb-4" style="display:none;">Download QR Code</button>
                <noscript>
                    <div class="text-center mb-4">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code Absensi" class="img-fluid" style="max-width: 250px;">
                    </div>
                </noscript>
                <p>Scan QR di perangkat yang tersedia atau gunakan kamera ponsel untuk menandai hadir.</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <div class="card p-4">
                @if ($todayAttendance && $todayAttendance->check_in)
                    <h5 class="mb-3">Status Absensi Hari Ini</h5>
                    <p class="mb-1"><strong>Status:</strong> {{ $todayAttendance->status }}</p>
                    <p class="mb-1"><strong>Masuk:</strong> {{ optional($todayAttendance->check_in)->format('H:i:s') }}
                    </p>
                    <p class="mb-3"><strong>Pulang:</strong>
                        {{ optional($todayAttendance->check_out)->format('H:i:s') ?? 'Belum pulang' }}</p>

                    @if (!$todayAttendance->check_out)
                        <form method="POST" action="{{ route('attendance.checkout') }}">
                            @csrf
                            <button class="btn btn-primary w-100">Absensi Pulang</button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0" role="alert">
                            Anda sudah menyelesaikan absensi hari ini.
                        </div>
                    @endif
                @else
                    <h5 class="mb-3">Form Absensi Masuk</h5>
                    <form method="POST" action="{{ route('attendance.checkin') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status Absensi</label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Selfie</label>
                            <input type="file" name="selfie_photo" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
                        </div>
                        <button class="btn btn-primary w-100">Absensi Masuk</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var qrCodeText = @json($qrCodeValue);
            var qrContainer = document.getElementById('qrCodeContainer');
            var downloadBtn = document.getElementById('downloadQrBtn');

            if (!qrContainer || typeof QRCode === 'undefined') {
                return;
            }

            qrContainer.innerHTML = '';
            new QRCode(qrContainer, {
                text: qrCodeText,
                width: 250,
                height: 250,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            // Show download button and add click handler
            downloadBtn.style.display = 'inline-block';
            downloadBtn.addEventListener('click', function() {
                var canvas = qrContainer.querySelector('canvas');
                if (canvas) {
                    var link = document.createElement('a');
                    link.href = canvas.toDataURL('image/png');
                    link.download = 'qr-absensi-' + new Date().getTime() + '.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        });
    </script>
@endpush
