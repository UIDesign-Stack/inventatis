@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Data Peminjaman</h1>

        @can('loan.create')
            <a href="{{ route('loans.create') }}" class="btn btn-primary">+ Catat Peminjaman</a>
        @endcan
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Pencarian & filter status --}}
    <form method="GET" action="{{ route('loans.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari nama guru / nama atau kode barang">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
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
                        <td>{{ $loans->firstItem() + $loop->index }}</td>
                        <td>{{ \Carbon\Carbon::parse($loan->decided_at ?? $loan->loan_date)->format('d/m/Y') }}</td>
                        <td>
                            {{ $loan->returned_at
                                ? \Carbon\Carbon::parse($loan->returned_at)->format('d/m/Y')
                                : '-' }}
                        </td>
                        <td>{{ $loan->teacher->name }}</td>
                        <td>{{ $loan->item->name }} ({{ $loan->item->code }})</td>
                        <td>{{ $loan->quantity }} {{ $loan->item->unit }}</td>
                        <td>
                            @switch($loan->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-primary">Disetujui</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                    @break
                                @case('handed_over')
                                    <span class="badge bg-success">Dipinjam</span>
                                    @break
                                @case('returned')
                                    <span class="badge bg-dark">Dikembalikan</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-secondary">Dibatalkan</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-info">Detail</a>

                            @can('loan.create')
                                @if (in_array($loan->status, ['pending', 'rejected', 'cancelled'], true))
                                    <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus data peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $loans->links() }}
</div>
@endsection
