@extends('layouts.app')

@section('content')
<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('teachers.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Data guru
    </a>

    <div class="mb-4">
        <h1 class="h3 font-display fw-bold mb-1">Tambah guru</h1>
        <p class="inv-page-sub mb-0">Daftarkan guru baru. Kolom bertanda <span class="text-danger">*</span> wajib diisi.</p>
    </div>

    <form action="{{ route('teachers.store') }}" method="POST" class="card inv-panel">
        @csrf

        {{-- Identitas --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Identitas</h2>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="nip" class="form-label">NIP</label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                           class="form-control @error('nip') is-invalid @enderror">
                    @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Boleh dikosongkan.</div>
                </div>

                <div class="col-md-8">
                    <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Kontak dan tugas --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Kontak dan tugas</h2>

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
                    <label for="subject" class="form-label">Mapel / jabatan</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                           class="form-control @error('subject') is-invalid @enderror">
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="inv-form-foot">
            <a href="{{ route('teachers.index') }}" class="btn btn-outline-primary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan guru</button>
        </div>
    </form>
</div>
@endsection
