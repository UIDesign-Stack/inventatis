@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header halaman --}}
    <a href="{{ route('suppliers.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Data supplier
    </a>

    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">{{ $supplier->name }}</h1>
            <p class="inv-page-sub mb-0">Terdaftar {{ $supplier->created_at?->format('d/m/Y') ?? '-' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">Edit supplier</a>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Jumlah barang</div>
                <div class="inv-stat-value font-display fw-bold">{{ number_format($supplier->items_count) }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Telepon</div>
                <div class="inv-stat-value font-display fw-bold" style="font-size: 1.35rem;">
                    {{ $supplier->phone ?? '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Email</div>
                <div class="inv-stat-value font-display fw-bold text-break" style="font-size: 1.35rem;">
                    {{ $supplier->email ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Alamat --}}
    <div class="card inv-panel mb-3">
        <section class="inv-form-section border-bottom-0">
            <h2 class="inv-form-title">Alamat</h2>
            @if (filled($supplier->address))
                <p class="mb-0">{!! nl2br(e($supplier->address)) !!}</p>
            @else
                <p class="text-muted mb-0">-</p>
            @endif
        </section>
    </div>

    {{-- Barang yang pernah dikirim --}}
    <div class="card inv-panel">
        <div class="inv-toolbar">
            <h2 class="inv-form-title mb-0">Barang yang pernah dikirim</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Tanggal terima</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th class="text-end">Jumlah</th>
                        <th class="pe-4">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="ps-4 text-muted">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="inv-num text-nowrap">{{ $item->received_date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('items.show', $item) }}" class="inv-name">{{ $item->name }}</a>
                                <div class="inv-code">{{ $item->code }}</div>
                            </td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            <td class="text-end inv-num text-nowrap">
                                <span class="fw-semibold">{{ $item->stock }}</span>
                                <span class="text-muted">{{ $item->unit }}</span>
                            </td>
                            <td class="pe-4">
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
                                        <span class="inv-pill inv-pill--muted">{{ ucfirst($item->condition) }}</span>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="inv-empty">
                                    <p class="fw-semibold mb-1">Belum ada barang dari supplier ini</p>
                                    <p class="text-muted mb-0">Barang yang dicatat dari supplier ini akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
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
