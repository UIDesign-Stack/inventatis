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

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert inv-alert inv-alert--success" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert inv-alert inv-alert--danger" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert inv-alert inv-alert--danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

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

    {{-- ===================== AKSI ===================== --}}

    {{-- Keputusan: ACC / tolak / pending (hanya saat status pending) --}}
    @can('loan.approve')
        @if ($loan->isPending())
            <div class="card inv-panel mb-3">
                <section class="inv-form-section">
                    <h2 class="inv-form-title">Keputusan</h2>
                    <p class="text-muted small mb-3">
                        Jika disetujui, barang dicatat langsung diserahkan ke guru, nomor surat dibuat otomatis,
                        dan stok dikurangi {{ $loan->quantity }} {{ $loan->item->unit ?? '' }}.
                        @if ($loan->item)
                            Stok saat ini: <strong>{{ $loan->item->stock }} {{ $loan->item->unit }}</strong>.
                        @endif
                    </p>

                    <form method="POST" action="{{ route('loans.decide', $loan) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision"
                                       id="decision-approved" value="approved"
                                       @checked(old('decision') === 'approved') required>
                                <label class="form-check-label" for="decision-approved">Setujui (ACC)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision"
                                       id="decision-rejected" value="rejected"
                                       @checked(old('decision') === 'rejected')>
                                <label class="form-check-label" for="decision-rejected">Tolak</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="decision"
                                       id="decision-pending" value="pending"
                                       @checked(old('decision') === 'pending')>
                                <label class="form-check-label" for="decision-pending">Tunda (pending) dengan catatan</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status_note" class="form-label">
                                Alasan <span class="text-muted">(wajib jika ditolak atau di-pending)</span>
                            </label>
                            <textarea id="status_note" name="status_note" rows="3" maxlength="500"
                                      class="form-control @error('status_note') is-invalid @enderror">{{ old('status_note') }}</textarea>
                            @error('status_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan keputusan</button>
                    </form>
                </section>
            </div>
        @endif
    @endcan

    {{-- Pengembalian: barang sudah diserahkan (disetujui / dipinjam), belum dikembalikan --}}
    @can('loan.return')
        @if ($loan->isApproved() || $loan->isHandedOver())
            <div class="card inv-panel mb-3">
                <section class="inv-form-section">
                    <h2 class="inv-form-title">Pengembalian barang</h2>
                    <p class="text-muted small mb-3">
                        Stok akan bertambah {{ $loan->quantity }} {{ $loan->item->unit ?? '' }}
                        setelah pengembalian dicatat.
                    </p>

                    <form method="POST" action="{{ route('loans.return', $loan) }}"
                          onsubmit="return confirm('Catat pengembalian barang?')">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="return_note" class="form-label">
                                Catatan kondisi barang <span class="text-muted">(opsional)</span>
                            </label>
                            <textarea id="return_note" name="return_note" rows="2" maxlength="500"
                                      class="form-control">{{ old('return_note') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Catat pengembalian</button>
                    </form>
                </section>
            </div>
        @endif
    @endcan

    {{-- ===================== RINCIAN ===================== --}}
    <div class="card inv-panel mb-3">
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

                <dt class="col-sm-4 fw-normal text-muted">No. surat serah terima</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $loan->handover_letter_number ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Catatan status</dt>
                <dd class="col-sm-8 mb-0">
                    @if (filled($loan->status_note))
                        {!! nl2br(e($loan->status_note)) !!}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </dd>

                <dt class="col-sm-4 fw-normal text-muted">Catatan pengembalian</dt>
                <dd class="col-sm-8 mb-0">
                    @if (filled($loan->return_note))
                        {!! nl2br(e($loan->return_note)) !!}
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

                <dt class="col-sm-4 fw-normal text-muted">Waktu keputusan</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $loan->decided_at?->format('d/m/Y H:i') ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Diserahkan oleh (sarpras)</dt>
                <dd class="col-sm-8 mb-0">{{ $loan->sarpras->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Waktu diserahkan</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $loan->handed_over_at?->format('d/m/Y H:i') ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Pengembalian diterima oleh</dt>
                <dd class="col-sm-8 mb-0">{{ $loan->returner->name ?? '-' }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Waktu dikembalikan</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $loan->returned_at?->format('d/m/Y H:i') ?? '-' }}</dd>
            </dl>
        </section>
    </div>

    {{-- Hapus: hanya untuk yang belum diproses / ditolak / dibatalkan --}}
    @can('loan.create')
        @if (in_array($loan->status, ['pending', 'rejected', 'cancelled'], true))
            <form method="POST" action="{{ route('loans.destroy', $loan) }}"
                  onsubmit="return confirm('Hapus data peminjaman ini?')" class="mb-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Hapus data peminjaman</button>
            </form>
        @endif
    @endcan
</div>
@endsection
