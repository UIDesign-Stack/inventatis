@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Detail Supplier</h1>
        <div>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <table class="table table-bordered w-auto">
        <tr>
            <th>Nama</th>
            <td>{{ $supplier->name }}</td>
        </tr>
        <tr>
            <th>Telepon</th>
            <td>{{ $supplier->phone ?? '-' }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $supplier->email ?? '-' }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td>{{ $supplier->address ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jumlah Barang</th>
            <td>{{ $supplier->items_count }}</td>
        </tr>
        <tr>
            <th>Terdaftar</th>
            <td>{{ $supplier->created_at?->format('d/m/Y') }}</td>
        </tr>
    </table>

    <h2 class="h5 mt-4">Barang yang Pernah Dikirim</h2>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Terima</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $items->firstItem() + $loop->index }}</td>
                        <td>{{ $item->received_date->format('d/m/Y') }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td>{{ $item->stock }} {{ $item->unit }}</td>
                        <td>{{ ucfirst($item->condition) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada barang dari supplier ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
</div>
@endsection
