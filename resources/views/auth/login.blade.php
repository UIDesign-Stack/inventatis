@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5 col-xl-4">

            {{-- Judul --}}
            <div class="text-center mb-4 mt-md-4">

                <h1 class="h3 font-display fw-bold mb-1">Masuk</h1>
                <p class="inv-page-sub mb-0">Masuk untuk mengelola inventaris.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="card inv-panel">
                @csrf

                <div class="inv-form-section border-bottom-0">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required autocomplete="email" autofocus>
                        @error('email')
                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <!-- <div class="d-flex justify-content-between align-items-baseline">
                            <label for="password" class="form-label mb-2">Password</label>
                            @if (Route::has('password.request'))
                                <a class="small" href="{{ route('password.request') }}">Lupa password?</a>
                            @endif
                        </div> -->
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
