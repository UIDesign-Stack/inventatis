@extends('layouts.app')

@section('content')
<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('items.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Data barang
    </a>

    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">{{ $item->name }}</h1>
            <p class="inv-page-sub mb-0">Kode {{ $item->code }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('items.edit', $item) }}" class="btn btn-primary">Edit barang</a>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Stok</div>
                <div class="inv-stat-value font-display fw-bold">
                    {{ $item->stock }} <span class="fs-6 fw-medium text-muted">{{ $item->unit }}</span>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Kondisi</div>
                <div class="mt-2">
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
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Tanggal terima</div>
                <div class="inv-stat-value font-display fw-bold" style="font-size: 1.5rem;">
                    {{ $item->received_date->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Rincian --}}
    <div class="card inv-panel">
        <section class="inv-form-section">
            <h2 class="inv-form-title">Informasi barang</h2>

            <dl class="row mb-0 gy-3">
                <dt class="col-sm-4 fw-normal text-muted">Kategori</dt>
                <dd class="col-sm-8 mb-0">{{ $item->category->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Supplier</dt>
                <dd class="col-sm-8 mb-0">{{ $item->supplier->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Keterangan</dt>
                <dd class="col-sm-8 mb-0">
                    @if (filled($item->description))
                        {!! nl2br(e($item->description)) !!}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </dd>
            </dl>
        </section>

        <section class="inv-form-section">
            <h2 class="inv-form-title">Riwayat</h2>

            <dl class="row mb-0 gy-3">
                <dt class="col-sm-4 fw-normal text-muted">Diterima / dicatat oleh</dt>
                <dd class="col-sm-8 mb-0">{{ $item->receiver->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Dibuat</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Diperbarui</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $item->updated_at?->format('d/m/Y H:i') ?? '-' }}</dd>
            </dl>
        </section>
    </div>
</div>
@endsection
