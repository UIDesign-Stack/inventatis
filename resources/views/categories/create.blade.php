@extends('layouts.app')

@section('content')
<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('categories.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Kategori barang
    </a>

    <div class="mb-4">
        <h1 class="h3 font-display fw-bold mb-1">Tambah kategori</h1>
        <p class="inv-page-sub mb-0">Buat kategori baru untuk mengelompokkan barang. Kolom bertanda <span class="text-danger">*</span> wajib diisi.</p>
    </div>

    <form action="{{ route('categories.store') }}" method="POST" class="card inv-panel">
        @csrf

        <section class="inv-form-section">
            <h2 class="inv-form-title">Data kategori</h2>

            <div class="row g-3">
                <div class="col-12">
                    <label for="name" class="form-label">Nama kategori <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        <div class="inv-form-foot">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan kategori</button>
        </div>
    </form>
</div>
@endsection
