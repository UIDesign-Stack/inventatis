@extends('layouts.app')

@section('content')
{{-- Khusus halaman ini: kertas A4 portrait (aturan @page di layout adalah landscape untuk laporan) --}}
<style>
    @page { size: A4 portrait; margin: 10mm; }
</style>

<div class="container">

    {{-- Bagian ini tidak ikut tercetak --}}
    <div class="no-print">
        <a href="{{ route('items.index') }}" class="inv-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Data Barang
        </a>

        <div class="inv-page-head">
            <div>
                <h1 class="h3 mb-1">Cetak Label Barang</h1>
                <div class="inv-page-sub">
                    {{ $labels->count() }} label dari {{ $items->count() }} barang.
                    Tempel di barang agar mudah dicari dan dipindai.
                </div>
            </div>

            @if ($labels->isNotEmpty())
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/></svg>
                    Cetak
                </button>
            @endif
        </div>

        @if (session('error'))
            <div class="alert inv-alert inv-alert--danger">{{ session('error') }}</div>
        @endif

        <div class="card inv-panel mb-3">
            <form method="GET" action="{{ route('items.labels') }}" class="inv-toolbar align-items-center">
                @foreach ($ids as $id)
                    <input type="hidden" name="ids[]" value="{{ $id }}">
                @endforeach

                <div class="inv-filter">
                    <select name="mode" class="form-select" aria-label="Jenis label">
                        <option value="type" @selected($mode === 'type')>Satu label per jenis barang</option>
                        <option value="unit" @selected($mode === 'unit')>Label bernomor per unit (sesuai stok)</option>
                    </select>
                </div>

                <div class="inv-filter">
                    <div class="input-group">
                        <span class="input-group-text">Salinan</span>
                        <input type="number" name="copies" min="1" max="50" value="{{ $copies }}" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-outline-secondary">Terapkan</button>
            </form>
        </div>

        <p class="small text-muted mb-3">
            Salinan hanya berlaku untuk mode per jenis barang. Pada mode per unit, jumlah label sama dengan stok barang
            dan nomornya dibuat otomatis, misalnya <span class="inv-code">KODE-001</span>, <span class="inv-code">KODE-002</span>.
        </p>
    </div>

    {{-- Lembar label --}}
    <div class="card inv-panel print-area">
        @if ($labels->isEmpty())
            <div class="inv-empty text-muted no-print">
                Tidak ada label untuk dicetak. Pada mode per unit, barang yang stoknya 0 tidak menghasilkan label.
            </div>
        @else
            <div class="label-sheet">
                @foreach ($labels as $label)
                    <div class="label-card">
                        <img class="label-qr" src="{{ $label['qr'] }}" alt="QR {{ $label['code'] }}">
                        <div class="label-body">
                            <div class="label-org">{{ config('app.name', 'Inventaris Sekolah') }}</div>
                            <div class="label-name">{{ $label['name'] }}</div>
                            <div class="label-code">{{ $label['code'] }}</div>
                            @if ($label['category'])
                                <div class="label-meta">{{ $label['category'] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
