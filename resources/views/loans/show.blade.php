@extends('layouts.app')

@section('content')
<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('loans.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Data peminjaman
    </a>

    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">Detail peminjaman</h1>
            <p class="inv-page-sub mb-0">
                {{ $loan->item->name ?? '-' }} &middot; {{ $loan->teacher->name ?? '-' }}
            </p>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Status</div>
                <div class="mt-2">
                    @switch($loan->status)
                        @case('pending')
                            <span class="inv-pill inv-pill--warn">Pending</span>
                            @break
                        @case('approved')
                            <span class="inv-pill inv-pill--brand">Disetujui</span>
                            @break
                        @case('rejected')
                            <span class="inv-pill inv-pill--bad">Ditolak</span>
                            @break
                        @case('handed_over')
                            <span class="inv-pill inv-pill--ok">Dipinjam</span>
                            @break
                        @case('returned')
                            <span class="inv-pill inv-pill--muted">Dikembalikan</span>
                            @break
                        @case('cancelled')
                            <span class="inv-pill inv-pill--muted">Dibatalkan</span>
                            @break
                        @default
                            <span class="text-muted">-</span>
                    @endswitch
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Jumlah</div>
                <div class="inv-stat-value font-display fw-bold">
                    {{ $loan->quantity }}
                    <span class="fs-6 fw-medium text-muted">{{ $loan->item->unit ?? '' }}</span>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Tanggal pinjam</div>
                <div class="inv-stat-value font-display fw-bold" style="font-size: 1.5rem;">
                    @php($loanDate = $loan->decided_at ?? $loan->loan_date)
                    {{ $loanDate ? \Carbon\Carbon::parse($loanDate)->format('d/m/Y') : '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Rincian --}}
    <div class="card inv-panel">
        <section class="inv-form-section">
            <h2 class="inv-form-title">Informasi peminjaman</h2>

            <dl class="row mb-0 gy-3">
                <dt class="col-sm-4 fw-normal text-muted">Peminjam (guru)</dt>
                <dd class="col-sm-8 mb-0">
                    @if ($loan->teacher)
                        <a href="{{ route('teachers.show', $loan->teacher) }}">{{ $loan->teacher->name }}</a>
                    @else
                        -
                    @endif
                </dd>

                <dt class="col-sm-4 fw-normal text-muted">Barang</dt>
                <dd class="col-sm-8 mb-0">
                    @if ($loan->item)
                        {{ $loan->item->name }} <span class="inv-code">({{ $loan->item->code }})</span>
                    @else
                        -
                    @endif
                </dd>

                <dt class="col-sm-4 fw-normal text-muted">Catatan status</dt>
                <dd class="col-sm-8 mb-0">
                    @if (filled($loan->status_note))
                        {!! nl2br(e($loan->status_note)) !!}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </dd>
            </dl>
        </section>

        <section class="inv-form-section">
            <h2 class="inv-form-title">Riwayat proses</h2>

            <dl class="row mb-0 gy-3">
                <dt class="col-sm-4 fw-normal text-muted">Diajukan oleh</dt>
                <dd class="col-sm-8 mb-0">{{ $loan->creator->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Diputuskan oleh</dt>
                <dd class="col-sm-8 mb-0">{{ $loan->decider->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Diserahkan oleh (sarpras)</dt>
                <dd class="col-sm-8 mb-0">{{ $loan->sarpras->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Waktu diserahkan</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $loan->handed_over_at?->format('d/m/Y H:i') ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Pengembalian diterima oleh</dt>
                <dd class="col-sm-8 mb-0">{{ $loan->returner->name ?? '-' }}</dd>
            </dl>
        </section>
    </div>
</div>
@endsection
