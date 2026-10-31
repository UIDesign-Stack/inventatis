{{-- Data pengembalian: tampil hanya jika peminjaman sudah dikembalikan --}}
@if ($loan->status === 'returned')
    <div class="card mb-3">
        <div class="card-header">Data Pengembalian</div>
        <div class="card-body">
            <table class="table table-bordered w-auto mb-0">
                <tr>
                    <th>Tanggal Pinjam</th>
                    <td>{{ \Carbon\Carbon::parse($loan->decided_at ?? $loan->loan_date)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Kembali</th>
                    <td>
                        {{ $loan->returned_at
                            ? \Carbon\Carbon::parse($loan->returned_at)->format('d/m/Y H:i')
                            : '-' }}
                    </td>
                </tr>
                <tr>
                    <th>Dicatat oleh</th>
                    <td>{{ $loan->returner?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>{{ $loan->return_note ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
@endif
