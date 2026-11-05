
@extends('layouts.app')

@section('content')

<div class="inv-app">
    <div class="inv-main">
        <div class="container">

            {{-- HEADER HALAMAN --}}
            <div class="inv-page-head no-print">
                <div>
                    <h1 class="h3 fw-bold mb-1">Laporan Inventaris Barang</h1>
                    <p class="inv-page-sub mb-0">
                        Kelola dan cetak laporan data inventaris barang sekolah.
                    </p>
                </div>

                <button type="button" onclick="window.print()"
                    class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="bi bi-printer"></i>
                    Cetak Laporan
                </button>
            </div>

            {{-- FILTER LAPORAN --}}
            <div class="card mb-4 no-print">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    Filter Laporan
                </div>

                <div class="card-body">
                    <form action="{{ route('items.report') }}" method="GET">

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label for="from" class="form-label">
                                    Tanggal Mulai
                                </label>
                                <input type="date"
                                    name="from"
                                    id="from"
                                    value="{{ request('from') }}"
                                    class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="to" class="form-label">
                                    Tanggal Selesai
                                </label>
                                <input type="date"
                                    name="to"
                                    id="to"
                                    value="{{ request('to') }}"
                                    class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="supplier_id" class="form-label">
                                    Supplier
                                </label>
                                <select name="supplier_id"
                                    id="supplier_id"
                                    class="form-select">

                                    <option value="">Semua Supplier</option>

                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            @selected(request('supplier_id') == $supplier->id)>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Terapkan Filter
                            </button>

                            <a href="{{ route('items.report') }}"
                                class="btn btn-outline-primary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            {{-- RINGKASAN --}}
            <div class="row g-3 mb-4 no-print">

                <div class="col-md-6">
                    <div class="inv-stat">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="inv-stat-label">
                                    Total Jenis Barang
                                </div>
                                <div class="inv-stat-value">
                                    {{ $items->count() }}
                                </div>
                                <div class="inv-stat-note">
                                    Barang berdasarkan filter
                                </div>
                            </div>

                            <div class="inv-quick-icon">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="inv-stat">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="inv-stat-label">
                                    Total Stok Barang
                                </div>
                                <div class="inv-stat-value">
                                    {{ number_format($totalStock, 0, ',', '.') }}
                                </div>
                                <div class="inv-stat-note">
                                    Akumulasi seluruh stok barang
                                </div>
                            </div>

                            <div class="inv-quick-icon">
                                <i class="bi bi-stack fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- AREA LAPORAN --}}
            <div class="card inv-panel print-area">
                <div class="card-body p-3 p-md-4">

                    {{-- KOP SURAT --}}
                    <div class="report-header">

                        <div class="school-logo">
                            <img src="{{ asset('images/logo_surat.png') }}"
                                alt="Logo Sekolah">
                        </div>

                        <div class="school-info">
                            <h1>PEMERINTAH KABUPATEN BEKASI</h1>
                            <h1>DINAS PENDIDIKAN</h1>
                            <h1>SMPN 6 BABELAN</h1>
                            <p>
                                Jl. Pintu Gerbang Timur Villa Gading Harapan, Rt.04 Rw.02 Kel. Kebalen, Kec. Babelan, Kab Bekasi, 17610
                            </p>
                            <p>
                                email : smpn.6bal@gmail.com
                            </p>
                        </div>

                    </div>

                    <div class="kop-line"></div>

                    {{-- JUDUL LAPORAN --}}
                    <div class="report-title text-center my-4">
                        <h2>LAPORAN DATA INVENTARIS BARANG</h2>

                        <p>
                            Periode:
                            {{ request('from')
                                ? \Carbon\Carbon::parse(request('from'))->translatedFormat('d F Y')
                                : 'Semua Tanggal' }}

                            s.d.

                            {{ request('to')
                                ? \Carbon\Carbon::parse(request('to'))->translatedFormat('d F Y')
                                : 'Sekarang' }}
                        </p>

                        @if(request('supplier_id'))
                            <p>
                                Supplier:
                                {{ $suppliers->firstWhere('id', request('supplier_id'))?->name ?? '-' }}
                            </p>
                        @endif
                    </div>

                    {{-- INFORMASI LAPORAN --}}
                    <div class="report-meta">
                        <p>
                            <strong>Tanggal Cetak:</strong>
                            {{ now()->translatedFormat('d F Y') }}
                        </p>

                        <p>
                            <strong>Jumlah Data:</strong>
                            {{ $items->count() }} jenis barang
                        </p>
                    </div>

                    {{-- TABEL BARANG --}}
                    <div class="table-responsive report-table-wrap">
                        <table class="report-table">

                            <thead>
                                <tr>
                                    <th width="4%">No</th>
                                    <th width="12%">Kode Barang</th>
                                    <th width="20%">Nama Barang</th>
                                    <th width="13%">Kategori</th>
                                    <th width="11%">Tanggal Masuk</th>
                                    <th width="8%">Jumlah</th>
                                    <th width="7%">Satuan</th>
                                    <th width="10%">Kondisi</th>
                                    <th width="15%">Supplier</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($items as $item)
                                    <tr>
                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>{{ $item->code }}</td>

                                        <td>{{ $item->name }}</td>

                                        <td>
                                            {{ $item->category?->name ?? '-' }}
                                        </td>

                                        <td class="text-center">
                                            {{ $item->received_date
                                                ? \Carbon\Carbon::parse($item->received_date)->format('d/m/Y')
                                                : '-' }}
                                        </td>

                                        <td class="text-center">
                                            {{ number_format($item->stock, 0, ',', '.') }}
                                        </td>

                                        <td class="text-center">
                                            {{ $item->unit }}
                                        </td>

                                        <td class="text-center">
                                            {{ ucfirst($item->condition) }}
                                        </td>

                                        <td>
                                            {{ $item->supplier?->name ?? '-' }}
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Tidak ada data barang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if($items->isNotEmpty())
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">
                                            TOTAL STOK
                                        </th>

                                        <th class="text-center">
                                            {{ number_format($totalStock, 0, ',', '.') }}
                                        </th>

                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            @endif

                        </table>
                    </div>

                    {{-- TANDA TANGAN --}}
                    <div class="signature-section">

                        <div class="signature-box">
                            <p>Mengetahui,</p>
                            <p>Kepala Sekolah</p>

                            <div class="signature-space"></div>

                            <p class="signature-name">
                                Mardianto, S.Pd
                            </p>

                            <p>NIP. 196704011998031005</p>
                        </div>

                        <div class="signature-box">
                            <p>
                                {{ config('app.school_city', 'Babelan') }},
                                {{ now()->translatedFormat('d F Y') }}
                            </p>

                            <p>Petugas Inventaris</p>

                            <div class="signature-space"></div>

                            <p class="signature-name">
                                {{ auth()->user()->name ?? 'Nama Petugas' }}
                            </p>

                            <p>NIP. 200304272025211002</p>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
