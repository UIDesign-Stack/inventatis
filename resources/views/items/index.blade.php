@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header halaman --}}
    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">Data barang</h1>
            <p class="inv-page-sub mb-0">{{ number_format($items->total()) }} barang tercatat</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('stock-in.report')
                <a href="{{ route('items.report') }}" class="btn btn-outline-primary">Laporan barang masuk</a>
            @endcan
            <a href="{{ route('items.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Barang masuk
            </a>
        </div>
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert inv-alert inv-alert--success" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert inv-alert inv-alert--danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card inv-panel">

        {{-- Pencarian & filter kategori --}}
        <form method="GET" action="{{ route('items.index') }}" class="inv-toolbar">
            <div class="inv-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama atau kode barang"
                       aria-label="Cari nama atau kode barang">
            </div>

            <select name="category_id" class="form-select inv-filter" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Cari</button>
                @if (request()->filled('search') || request()->filled('category_id'))
                    <a href="{{ route('items.index') }}" class="btn btn-outline-primary">Reset</a>
                @endif
            </div>
        </form>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th class="">No</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th class="">Stok</th>
                        <th>Kondisi</th>
                        <th>Tanggal terima</th>
                        <th class="">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class=" text-muted">{{ $items->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->name }}</div>
                                <div class="inv-code">{{ $item->code }}</div>
                            </td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            <td>{{ $item->supplier->name ?? '-' }}</td>
                            <td class="inv-num">
                                <span class="fw-semibold">{{ $item->stock }}</span>
                                <span class="text-muted">{{ $item->unit }}</span>
                            </td>
                            <td>
                                @switch($item->condition)
                                    @case('baik')
                                        <span class="inv-pill inv-pill--ok">Baik</span>
                                        @break
                                    @case('rusak')
                                        <span class="inv-pill inv-pill--bad">Rusak</span>
                                        @break
                                    @case('perbaikan')
                                        <span class="inv-pill inv-pill--warn">Perbaikan</span>
                                        @break
                                    @default
                                        <span class="text-muted">-</span>
                                @endswitch
                            </td>
                            <td class="inv-num text-nowrap">{{ $item->received_date->format('d/m/Y') }}</td>
                            <td class="">
                                <div class="inv-actions">
                                    <a href="{{ route('items.show', $item) }}" class="inv-icon-btn"
                                       title="Detail" aria-label="Detail {{ $item->name }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>

                                    <a href="{{ route('items.edit', $item) }}" class="inv-icon-btn"
                                       title="Edit" aria-label="Edit {{ $item->name }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    </a>

                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus barang {{ e($item->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inv-icon-btn inv-icon-btn--danger"
                                                title="Hapus" aria-label="Hapus {{ $item->name }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m6 6 1 14h10l1-14"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="inv-empty">
                                    @if (request()->filled('search') || request()->filled('category_id'))
                                        <p class="fw-semibold mb-1">Tidak ada barang yang cocok</p>
                                        <p class="text-muted mb-3">Coba kata kunci lain atau pilih kategori yang berbeda.</p>
                                        <a href="{{ route('items.index') }}" class="btn btn-outline-primary">Reset pencarian</a>
                                    @else
                                        <p class="fw-semibold mb-1">Belum ada data barang</p>
                                        <p class="text-muted mb-3">Catat barang masuk pertama untuk mulai mengisi inventaris.</p>
                                        <a href="{{ route('items.create') }}" class="btn btn-primary">Tambah barang masuk</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info jumlah + paginasi --}}
        @if ($items->total() > 0)
            <div class="inv-panel-foot">
                <span class="text-muted small">
                    Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }} barang
                </span>
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
