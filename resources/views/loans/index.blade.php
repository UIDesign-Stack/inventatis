@extends('layouts.app')

@section('content')
@php
    // Label dan varian warna (.inv-pill--*) untuk tiap status peminjaman
    $pills = [
        'pending'     => ['Pending',      'warn'],
        'approved'    => ['Disetujui',    'info'],
        'rejected'    => ['Ditolak',      'bad'],
        'handed_over' => ['Dipinjam',     'ok'],
        'returned'    => ['Dikembalikan', 'dark'],
        'cancelled'   => ['Dibatalkan',   'muted'],
    ];

    $total = method_exists($loans, 'total') ? $loans->total() : null;
@endphp

<div class="container">
    <div class="inv-page-head">
        <div>
            <h1 class="h3 mb-1">Data Peminjaman</h1>
            <div class="inv-page-sub">Catat, setujui, dan pantau peminjaman barang oleh guru.</div>
        </div>

        @can('loan.create')
            <a href="{{ route('loans.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Catat Peminjaman
            </a>
        @endcan
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert inv-alert inv-alert--success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert inv-alert inv-alert--danger">{{ session('error') }}</div>
    @endif

    <div class="card inv-panel">
        {{-- Pencarian & filter status --}}
        <form method="GET" action="{{ route('loans.index') }}" class="inv-toolbar">
            <div class="inv-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama guru / nama atau kode barang">
            </div>

            <div class="inv-filter">
                <select name="status" class="form-select" aria-label="Filter status">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">Reset</a>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Guru</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        <tr>
                            <td class="inv-num text-muted">{{ $loans->firstItem() + $loop->index }}</td>
                            <td class="inv-num text-nowrap">{{ \Carbon\Carbon::parse($loan->decided_at ?? $loan->loan_date)->format('d/m/Y') }}</td>
                            <td class="inv-num text-nowrap">
                                {{ $loan->returned_at
                                    ? \Carbon\Carbon::parse($loan->returned_at)->format('d/m/Y')
                                    : '-' }}
                            </td>
                            <td class="fw-semibold">{{ $loan->teacher->name }}</td>
                            <td>
                                <div>{{ $loan->item->name }}</div>
                                <div class="inv-code">{{ $loan->item->code }}</div>
                            </td>
                            <td class="inv-num text-nowrap">{{ $loan->quantity }} {{ $loan->item->unit }}</td>
                            <td>
                                @php($pill = $pills[$loan->status] ?? [ucfirst((string) $loan->status), 'muted'])
                                <span class="inv-pill inv-pill--{{ $pill[1] }}">{{ $pill[0] }}</span>
                            </td>
                            <td>
                                <div class="inv-actions">
                                    <a href="{{ route('loans.show', $loan) }}" class="inv-icon-btn" title="Detail" aria-label="Detail peminjaman">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>

                                    @can('loan.create')
                                        @if (in_array($loan->status, ['pending', 'rejected', 'cancelled'], true))
                                            <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus data peminjaman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inv-icon-btn inv-icon-btn--danger" title="Hapus" aria-label="Hapus peminjaman">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="inv-empty text-muted">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="inv-panel-foot">
            <div class="small text-muted">
                @if ($loans->count())
                    Menampilkan {{ $loans->firstItem() }}–{{ $loans->lastItem() }}@if ($total !== null) dari {{ number_format($total, 0, ',', '.') }} data @endif
                @else
                    Tidak ada data
                @endif
            </div>
            {{ $loans->links() }}
        </div>
    </div>
</div>
@endsection
