@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <h2 class="page-title mb-3">Edit Shift Kerja</h2>
                @include('partials.alerts')
                <form method="POST" action="{{ route('shifts.update', $shift) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Shift</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $shift->name) }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="text" name="start_time" class="form-control time24"
                            value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i:s')) }}"
                            inputmode="numeric" maxlength="8" placeholder="HH:MM:SS" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="text" name="end_time" class="form-control time24"
                            value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i:s')) }}"
                            inputmode="numeric" maxlength="8" placeholder="HH:MM:SS" required>
                    </div>
                    <button class="btn btn-primary">Perbarui</button>
                    <a href="{{ route('shifts.index') }}" class="btn btn-outline-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            function normalize24(inp) {
                var v = inp.value.trim();
                if (!v) return;
                var m = v.match(/^(\d{1,2}):?(\d{2})?:?(\d{2})?\s*([ap]m)?$/i);
                if (m) {
                    var h = parseInt(m[1], 10);
                    var min = m[2] || '00';
                    var ampm = m[3];
                    if (ampm) {
                        if (/p/i.test(ampm) && h < 12) h += 12;
                        if (/a/i.test(ampm) && h == 12) h = 0;
                    }
                    if (isNaN(h)) return;
                    h = (h < 10 ? '0' + h : '' + h);
                    inp.value = h + ':' + (min + '').padStart(2, '0') + ':' + (sec + '').padStart(2, '0');
                    return;
                }
                var digits = v.replace(/\D/g, '');
                if (digits.length === 3) digits = '0' + digits;
                if (digits.length === 4) {
                    inp.value = digits.slice(0, 2) + ':' + digits.slice(2);
                }
            }

            document.querySelectorAll('input.time24').forEach(function(el) {
                el.addEventListener('blur', function() {
                    normalize24(el);
                });
                el.addEventListener('change', function() {
                    normalize24(el);
                });
            });
        })();
    </script>
@endpush
