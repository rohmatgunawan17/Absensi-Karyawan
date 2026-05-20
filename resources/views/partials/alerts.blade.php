@if ($errors->any())
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
@endif
