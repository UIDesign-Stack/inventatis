@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Detail Guru</h1>
        <div>
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <table class="table table-bordered w-auto">
        <tr>
            <th>NIP</th>
            <td>{{ $teacher->nip ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $teacher->name }}</td>
        </tr>
        <tr>
            <th>Telepon</th>
            <td>{{ $teacher->phone ?? '-' }}</td>
        </tr>
        <tr>
            <th>Mapel / Jabatan</th>
            <td>{{ $teacher->subject ?? '-' }}</td>
        </tr>
        <tr>
            <th>Terdaftar</th>
            <td>{{ $teacher->created_at?->format('d/m/Y') }}</td>
        </tr>
    </table>

    <h2 class="h5 mt-4">Riwayat Peminjaman</h2>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pinjam</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Diserahkan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loans as $loan)
                    <tr>
                        <td>{{ $loans->firstItem() + $loop->index }}</td>
                        <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
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
                                    <span class="badge bg-success">Diserahkan</span>
                                    @break
                            @endswitch
                        </td>
                        <td>{{ $loan->status_note ?? '-' }}</td>
                        <td>{{ $loan->handed_over_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada riwayat peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $loans->links() }}
</div>
@endsection
