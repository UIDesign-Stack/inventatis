@extends('layouts.app')

@section('content')
@php
    $statusLabels = \App\Http\Controllers\LoanController::STATUSES;
@endphp

<div class="container">
    <div class="inv-page-head">
        <div>
            <h1 class="h3 mb-1">Pengembalian Barang</h1>
            <div class="inv-page-sub">
                Daftar peminjaman yang sudah disetujui atau sedang dipinjam dan belum dikembalikan.
                Stok bertambah kembali otomatis saat pengembalian dicatat.
            </div>
        </div>
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert inv-alert inv-alert--success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert inv-alert inv-alert--danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert inv-alert inv-alert--danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CATAT PENGEMBALIAN --}}
    <div class="card inv-panel mb-4">
        @if ($returnable->isEmpty())
            <div class="inv-form-section border-bottom-0">
                <h2 class="inv-form-title">Catat Pengembalian</h2>
                <p class="mb-0 text-muted">Tidak ada peminjaman yang perlu dikembalikan.</p>
            </div>
        @else
            <form id="return-form" method="POST" action=""
                  data-url="{{ route('loans.return', ['loan' => '__ID__']) }}">
                @csrf
                @method('PATCH')

                <div class="inv-form-section">
                    <h2 class="inv-form-title">Catat Pengembalian</h2>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="return-loan" class="form-label">Barang yang dikembalikan <span class="text-danger">*</span></label>
                            <select id="return-loan" class="form-select" required>
                                <option value="">-- Pilih peminjaman --</option>
                                @foreach ($returnable as $row)
                                    <option value="{{ $row->id }}">
                                        {{ $row->teacher->name }} - {{ $row->item->name }}
                                        ({{ $row->quantity }} {{ $row->item->unit }}),
                                        {{ $statusLabels[$row->status] ?? $row->status }},
                                        pinjam {{ \Carbon\Carbon::parse($row->decided_at ?? $row->loan_date)->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label for="return_note" class="form-label">Catatan</label>
                            <input type="text" id="return_note" name="return_note" maxlength="500"
                                   class="form-control" placeholder="Opsional, misalnya kondisi barang">
                        </div>
                    </div>
                </div>

                <div class="inv-form-foot">
                    <button type="submit" class="btn btn-primary">Simpan Pengembalian</button>
                </div>
            </form>
        @endif
    </div>

    {{-- DAFTAR YANG BELUM DIKEMBALIKAN --}}
    <h2 class="h5 mb-3">Belum Dikembalikan</h2>

    <div class="card inv-panel">
        <form method="GET" action="{{ route('loans.returns') }}" class="inv-toolbar">
            <div class="inv-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama guru / nama atau kode barang">
            </div>

            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="{{ route('loans.returns') }}" class="btn btn-outline-secondary">Reset</a>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Guru</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Keluar</th>
                        <th>Status</th>
                        <th>Lama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($loans as $loan)
                        @php
                            // lama dihitung dari tanggal keluar; jika belum diserahkan, dari tanggal pinjam
                            $startAt = \Carbon\Carbon::parse($loan->handed_over_at ?? $loan->decided_at ?? $loan->loan_date);
                            $days = (int) $startAt->copy()->startOfDay()->diffInDays(now()->startOfDay());
                        @endphp
                        <tr>
                            <td class="inv-num text-muted">{{ $loans->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $loan->teacher->name }}</td>
                            <td>
                                <div>{{ $loan->item->name }}</div>
                                <div class="inv-code">{{ $loan->item->code }}</div>
                            </td>
                            <td class="inv-num text-nowrap">{{ $loan->quantity }} {{ $loan->item->unit }}</td>
                            <td class="inv-num text-nowrap">{{ \Carbon\Carbon::parse($loan->decided_at ?? $loan->loan_date)->format('d/m/Y') }}</td>
                            <td class="inv-num text-nowrap">
                                {{ $loan->handed_over_at
                                    ? \Carbon\Carbon::parse($loan->handed_over_at)->format('d/m/Y')
                                    : '-' }}
                            </td>
                            <td>
                                @if ($loan->status === 'handed_over')
                                    <span class="inv-pill inv-pill--ok">{{ $statusLabels['handed_over'] ?? 'Dipinjam' }}</span>
                                @else
                                    <span class="inv-pill inv-pill--info">{{ $statusLabels['approved'] ?? 'Disetujui' }}</span>
                                @endif
                            </td>
                            <td class="inv-num text-nowrap">{{ $days }} hari</td>
                            <td>
                                <div class="inv-actions align-items-center">
                                    <form action="{{ route('loans.return', $loan) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Catat pengembalian {{ $loan->item->name }} dari {{ $loan->teacher->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-primary">Kembalikan</button>
                                    </form>

                                    <a href="{{ route('loans.show', $loan) }}" class="inv-icon-btn" title="Detail" aria-label="Detail peminjaman">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="inv-empty text-muted">Tidak ada peminjaman yang perlu dikembalikan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="inv-panel-foot">
            <div class="small text-muted">
                @if ($loans->count())
                    Menampilkan {{ $loans->firstItem() }}–{{ $loans->lastItem() }}@if (method_exists($loans, 'total')) dari {{ number_format($loans->total(), 0, ',', '.') }} data @endif
                @else
                    Tidak ada data
                @endif
            </div>
            {{ $loans->links() }}
        </div>
    </div>
</div>

<script>
    // Isi alamat form sesuai peminjaman yang dipilih
    const returnForm = document.getElementById('return-form');

    if (returnForm) {
        returnForm.addEventListener('submit', function (e) {
            const select = document.getElementById('return-loan');

            if (! select.value) {
                e.preventDefault();
                return;
            }

            if (! confirm('Catat pengembalian untuk peminjaman yang dipilih?')) {
                e.preventDefault();
                return;
            }

            this.action = this.dataset.url.replace('__ID__', select.value);
        });
    }
</script>
@endsection
