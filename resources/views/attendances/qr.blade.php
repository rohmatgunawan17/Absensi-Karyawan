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
                @include('partials.alerts')

                @if ($todayAttendance && $todayAttendance->check_in && $todayAttendance->selfie_photo)
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
                    <h5 class="mb-3">{{ $todayAttendance?->check_in ? 'Lengkapi Absensi Masuk' : 'Form Absensi Masuk' }}</h5>
                    @if ($todayAttendance?->check_in && !$todayAttendance->selfie_photo)
                        <div class="alert alert-warning">
                            Data absensi sebelumnya belum memiliki foto selfie. Pilih ulang foto untuk menyelesaikan absensi.
                        </div>
                    @endif
                    <form id="checkInForm" method="POST" action="{{ route('attendance.checkin') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status Absensi</label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih status</option>
                                @foreach (['Hadir', 'Izin', 'Sakit', 'Alpha'] as $status)
                                    <option value="{{ $status }}" @selected(old('status') === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Selfie</label>
                            <input id="selfiePhoto" type="file" name="selfie_photo" class="form-control"
                                accept="image/jpeg,image/png" required>
                            <input id="selfieData" type="hidden" name="selfie_data">
                            <div id="selfieStatus" class="form-text text-muted">Format JPG atau PNG, maksimal 3 MB.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Keterangan tambahan...">{{ old('note') }}</textarea>
                        </div>
                        <button id="checkInButton" class="btn btn-primary w-100">Absensi Masuk</button>
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

            if (qrContainer && downloadBtn && typeof QRCode !== 'undefined') {
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
            }

            var checkInForm = document.getElementById('checkInForm');
            var selfieInput = document.getElementById('selfiePhoto');
            var selfieData = document.getElementById('selfieData');
            var selfieStatus = document.getElementById('selfieStatus');
            var checkInButton = document.getElementById('checkInButton');
            var selfieIsReady = false;

            if (checkInForm && selfieInput && selfieData) {
                selfieInput.addEventListener('change', function() {
                    var file = selfieInput.files && selfieInput.files[0];
                    selfieData.value = '';
                    selfieIsReady = false;

                    if (!file) {
                        selfieStatus.textContent = 'Format JPG atau PNG, maksimal 3 MB.';
                        return;
                    }

                    if (!['image/jpeg', 'image/png'].includes(file.type) || file.size > 3 * 1024 * 1024) {
                        selfieInput.value = '';
                        selfieStatus.textContent = 'Foto harus berformat JPG atau PNG dan maksimal 3 MB.';
                        selfieStatus.classList.add('text-danger');
                        return;
                    }

                    selfieStatus.classList.remove('text-danger');
                    selfieStatus.textContent = 'Menyiapkan foto...';

                    var reader = new FileReader();
                    reader.addEventListener('load', function() {
                        selfieData.value = reader.result;
                        selfieIsReady = true;
                        selfieStatus.textContent = 'Foto siap dikirim.';
                    });
                    reader.addEventListener('error', function() {
                        selfieStatus.textContent = 'Foto gagal dibaca. Silakan pilih ulang.';
                        selfieStatus.classList.add('text-danger');
                    });
                    reader.readAsDataURL(file);
                });

                checkInForm.addEventListener('submit', function(event) {
                    if (selfieInput.files.length && !selfieIsReady) {
                        event.preventDefault();
                        selfieStatus.textContent = 'Tunggu sebentar, foto masih disiapkan.';
                        return;
                    }

                    if (selfieData.value) {
                        selfieInput.removeAttribute('name');
                    }

                    checkInButton.disabled = true;
                    checkInButton.textContent = 'Mengirim...';
                });
            }
        });
    </script>
@endpush
