@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card p-4">
                <h2 class="page-title mb-3">Tambah Shift Kerja</h2>
                @include('partials.alerts')
                <form method="POST" action="{{ route('shifts.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Shift</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="text" name="start_time" class="form-control time24" value="{{ old('start_time') }}"
                            inputmode="numeric" maxlength="8" placeholder="HH:MM:SS" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="text" name="end_time" class="form-control time24" value="{{ old('end_time') }}"
                            inputmode="numeric" maxlength="8" placeholder="HH:MM:SS" required>
                    </div>
                    <button class="btn btn-primary">Simpan</button>
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
                // convert am/pm to 24h
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
                // numeric inputs: 830 -> 08:30:00, 1530 -> 15:30:00, 153015 -> 15:30:15
                var digits = v.replace(/\D/g, '');
                if (digits.length === 3) digits = '0' + digits; // 830 -> 0830
                if (digits.length === 4) {
                    inp.value = digits.slice(0, 2) + ':' + digits.slice(2) + ':00';
                } else if (digits.length === 6) {
                    inp.value = digits.slice(0, 2) + ':' + digits.slice(2, 4) + ':' + digits.slice(4, 6);
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
