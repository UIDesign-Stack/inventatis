@extends('layouts.app')

@section('content')
<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('suppliers.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Data supplier
    </a>

    <div class="mb-4">
        <h1 class="h3 font-display fw-bold mb-1">Tambah supplier</h1>
        <p class="inv-page-sub mb-0">Daftarkan supplier baru. Kolom bertanda <span class="text-danger">*</span> wajib diisi.</p>
    </div>

    <form action="{{ route('suppliers.store') }}" method="POST" class="card inv-panel">
        @csrf

        {{-- Identitas --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Identitas</h2>

            <div class="row g-3">
                <div class="col-12">
                    <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Kontak --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Kontak</h2>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="phone" class="form-label">Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea id="address" name="address" rows="3"
                              class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="inv-form-foot">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-primary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan supplier</button>
        </div>
    </form>
</div>
@endsection
