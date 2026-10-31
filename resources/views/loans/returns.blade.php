@extends('layouts.app')

@section('content')
@php
    $statusLabels = \App\Http\Controllers\LoanController::STATUSES;
@endphp

<div class="container">
    <div class="mb-3">
        <h1 class="h3 mb-1">Pengembalian Barang</h1>
        <p class="text-muted mb-0">
            Daftar peminjaman yang sudah disetujui atau sedang dipinjam dan belum dikembalikan.
            Stok bertambah kembali otomatis saat pengembalian dicatat.
        </p>
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CATAT PENGEMBALIAN --}}
    <div class="card mb-4">
        <div class="card-header">Catat Pengembalian</div>
        <div class="card-body">
            @if ($returnable->isEmpty())
                <p class="mb-0 text-muted">Tidak ada peminjaman yang perlu dikembalikan.</p>
            @else
                <form id="return-form" method="POST" action=""
                      data-url="{{ route('loans.return', ['loan' => '__ID__']) }}">
                    @csrf
                    @method('PATCH')

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

                    <button type="submit" class="btn btn-success mt-3">Simpan Pengembalian</button>
                </form>
            @endif
        </div>
    </div>

    {{-- DAFTAR YANG BELUM DIKEMBALIKAN --}}
    <h2 class="h5">Belum Dikembalikan</h2>

    <form method="GET" action="{{ route('loans.returns') }}" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari nama guru / nama atau kode barang">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <a href="{{ route('loans.returns') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
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
                        <td>{{ $loans->firstItem() + $loop->index }}</td>
                        <td>{{ $loan->teacher->name }}</td>
                        <td>{{ $loan->item->name }} ({{ $loan->item->code }})</td>
                        <td>{{ $loan->quantity }} {{ $loan->item->unit }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->decided_at ?? $loan->loan_date)->format('d/m/Y') }}</td>
                        <td>
                            {{ $loan->handed_over_at
                                ? \Carbon\Carbon::parse($loan->handed_over_at)->format('d/m/Y')
                                : '-' }}
                        </td>
                        <td>
                            @if ($loan->status === 'handed_over')
                                <span class="badge bg-success">{{ $statusLabels['handed_over'] ?? 'Dipinjam' }}</span>
                            @else
                                <span class="badge bg-primary">{{ $statusLabels['approved'] ?? 'Disetujui' }}</span>
                            @endif
                        </td>
                        <td>{{ $days }} hari</td>
                        <td>
                            <form action="{{ route('loans.return', $loan) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Catat pengembalian {{ $loan->item->name }} dari {{ $loan->teacher->name }}?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">Kembalikan</button>
                            </form>
                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada peminjaman yang perlu dikembalikan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $loans->links() }}
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
