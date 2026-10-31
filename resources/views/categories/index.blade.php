@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header halaman --}}
    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">Kategori barang</h1>
            <p class="inv-page-sub mb-0">Kelola kategori barang inventaris sekolah.</p>
        </div>
        <div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Tambah kategori
            </a>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert inv-alert inv-alert--success" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert inv-alert inv-alert--danger" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Daftar kategori --}}
    <div class="card inv-panel">

        <div class="inv-toolbar justify-content-between align-items-center">
            <h2 class="inv-form-title mb-0">Daftar kategori</h2>
            <span class="text-muted small">{{ $categories->count() }} kategori terdaftar</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 6%;">No</th>
                        <th style="width: 28%;">Nama kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-end pe-4" style="width: 14%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td class="text-muted">{{ filled($category->description) ? \Illuminate\Support\Str::limit($category->description, 100) : '-' }}</td>
                            <td class="pe-4">
                                <div class="inv-actions justify-content-end">
                                    <a href="{{ route('categories.edit', $category->id) }}" class="inv-icon-btn"
                                       title="Edit" aria-label="Edit {{ $category->name }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    </a>

                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm(@js('Hapus kategori '.$category->name.'?'))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inv-icon-btn inv-icon-btn--danger"
                                                title="Hapus" aria-label="Hapus {{ $category->name }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m6 6 1 14h10l1-14"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="inv-empty">
                                    <p class="fw-semibold mb-1">Belum ada kategori</p>
                                    <p class="text-muted mb-3">Tambahkan kategori barang terlebih dahulu.</p>
                                    <a href="{{ route('categories.create') }}" class="btn btn-primary">Tambah kategori</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->count() > 0)
            <div class="inv-panel-foot">
                <span class="text-muted small">Menampilkan {{ $categories->count() }} kategori</span>
            </div>
        @endif
    </div>
</div>
@endsection
