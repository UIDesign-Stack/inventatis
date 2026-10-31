@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header halaman --}}
    <a href="{{ route('teachers.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Data guru
    </a>

    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">{{ $teacher->name }}</h1>
            <p class="inv-page-sub mb-0">NIP {{ $teacher->nip ?? '-' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary">Edit guru</a>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Telepon</div>
                <div class="inv-stat-value font-display fw-bold" style="font-size: 1.35rem;">
                    {{ $teacher->phone ?? '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Mapel / jabatan</div>
                <div class="inv-stat-value font-display fw-bold" style="font-size: 1.35rem;">
                    {{ $teacher->subject ?? '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="inv-stat">
                <div class="inv-stat-label">Terdaftar</div>
                <div class="inv-stat-value font-display fw-bold" style="font-size: 1.35rem;">
                    {{ $teacher->created_at?->format('d/m/Y') ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat peminjaman --}}
    <div class="card inv-panel">
        <div class="inv-toolbar">
            <h2 class="inv-form-title mb-0">Riwayat peminjaman</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Tanggal pinjam</th>
                        <th>Barang</th>
                        <th class="text-end">Jumlah</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th class="pe-4">Diserahkan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        <tr>
                            <td class="ps-4 text-muted">{{ $loans->firstItem() + $loop->index }}</td>
                            <td class="inv-num text-nowrap">{{ $loan->loan_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="fw-semibold">{{ $loan->item->name }}</span>
                                <div class="inv-code">{{ $loan->item->code }}</div>
                            </td>
                            <td class="text-end inv-num text-nowrap">
                                <span class="fw-semibold">{{ $loan->quantity }}</span>
                                <span class="text-muted">{{ $loan->item->unit }}</span>
                            </td>
                            <td>
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
                                        <span class="inv-pill inv-pill--ok">Diserahkan</span>
                                        @break
                                    @default
                                        <span class="inv-pill inv-pill--muted">{{ ucfirst(str_replace('_', ' ', $loan->status)) }}</span>
                                @endswitch
                            </td>
                            <td>{{ $loan->status_note ?? '-' }}</td>
                            <td class="inv-num text-nowrap pe-4">{{ $loan->handed_over_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="inv-empty">
                                    <p class="fw-semibold mb-1">Belum ada riwayat peminjaman</p>
                                    <p class="text-muted mb-0">Peminjaman oleh guru ini akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($loans->hasPages())
            <div class="inv-panel-foot">
                <span class="text-muted small">
                    Menampilkan {{ $loans->firstItem() }}–{{ $loans->lastItem() }} dari {{ $loans->total() }} peminjaman
                </span>
                {{ $loans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
