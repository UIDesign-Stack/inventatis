
@extends('layouts.app')

@section('content')

<div class="inv-app">
    <div class="inv-main">
        <div class="container">

            {{-- HEADER HALAMAN --}}
            <div class="inv-page-head no-print">
                <div>
                    <h1 class="h3 fw-bold mb-1">Laporan Barang Keluar</h1>
                    <p class="inv-page-sub mb-0">
                        Laporan data barang yang telah diserahkan kepada guru.
                    </p>
                </div>

                <button type="button"
                    onclick="window.print()"
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
                    <form action="{{ route('loans.report') }}" method="GET">

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
                                <label for="teacher_id" class="form-label">
                                    Guru Penerima
                                </label>

                                <select name="teacher_id"
                                    id="teacher_id"
                                    class="form-select">

                                    <option value="">Semua Guru</option>

                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"
                                            @selected(request('teacher_id') == $teacher->id)>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status Peminjaman</label>

                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>

                                    <option value="pending" @selected(request('status') === 'pending')>
                                        Menunggu
                                    </option>

                                    <option value="approved" @selected(request('status') === 'approved')>
                                        Disetujui
                                    </option>

                                    <option value="rejected" @selected(request('status') === 'rejected')>
                                        Ditolak
                                    </option>

                                    <option value="handed_over" @selected(request('status') === 'handed_over')>
                                        Sudah Diserahkan
                                    </option>

                                    <option value="returned" @selected(request('status') === 'returned')>
                                        Dikembalikan
                                    </option>

                                    <option value="cancelled" @selected(request('status') === 'cancelled')>
                                        Dibatalkan
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Terapkan Filter
                            </button>

                            <a href="{{ route('loans.report') }}"
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
                                    Total Transaksi
                                </div>
                                <div class="inv-stat-value">
                                    {{ $loans->count() }}
                                </div>
                                <div class="inv-stat-note">
                                    Jumlah transaksi barang keluar
                                </div>
                            </div>

                            <div class="inv-quick-icon">
                                <i class="bi bi-journal-check fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="inv-stat">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="inv-stat-label">
                                    Total Barang Keluar
                                </div>
                                <div class="inv-stat-value">
                                    {{ number_format($totalQuantity, 0, ',', '.') }}
                                </div>
                                <div class="inv-stat-note">
                                    Akumulasi jumlah barang yang diserahkan
                                </div>
                            </div>

                            <div class="inv-quick-icon">
                                <i class="bi bi-box-seam fs-4"></i>
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
                            <img src="{{ asset('images/logo.jpg') }}"
                                alt="Logo Sekolah">
                        </div>

                        <div class="school-info">
                            <h2>PEMERINTAH KABUPATEN / KOTA</h2>
                            <h2>DINAS PENDIDIKAN</h2>
                            <h1>SMPN 6 BABELAN</h1>

                            <p>
                                Jl.Pintu Gerbang Timur Vila Gading Harapan, RT. 004, RW. 002
                            </p>

                            <p>
                                Email: sekolah@email.com |
                                Telepon: (021) XXXXX
                            </p>
                        </div>

                    </div>

                    <div class="kop-line"></div>

                    {{-- JUDUL LAPORAN --}}
                    <div class="report-title text-center my-4">
                        <h2>LAPORAN BARANG KELUAR / PEMINJAMAN</h2>

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

                        @if(request('teacher_id'))
                            <p>
                                Guru Penerima:
                                {{ $teachers->firstWhere('id', request('teacher_id'))?->name ?? '-' }}
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
                            <strong>Jumlah Transaksi:</strong>
                            {{ $loans->count() }} transaksi
                        </p>
                    </div>

                    {{-- TABEL BARANG KELUAR --}}
                    <div class="table-responsive report-table-wrap">
                        <table class="report-table">

                            <thead>
                                <tr>
                                    <th width="4%">No</th>
                                    <th width="12%">Kode Barang</th>
                                    <th width="19%">Nama Barang</th>
                                    <th width="10%">Kategori</th>
                                    <th width="16%">Guru Penerima</th>
                                    <th width="9%">Jumlah</th>
                                    <th width="10%">Tanggal Keluar</th>
                                    <th width="12%">Petugas Sarpras</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($loans as $loan)
                                    <tr>
                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            {{ $loan->item?->code ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $loan->item?->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $loan->item?->category?->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $loan->teacher?->name ?? '-' }}
                                        </td>

                                        <td class="text-center">
                                            {{ number_format($loan->quantity, 0, ',', '.') }}
                                        </td>

                                        <td class="text-center">
                                            {{ $loan->handed_over_at
                                                ? \Carbon\Carbon::parse($loan->handed_over_at)->format('d/m/Y')
                                                : '-' }}
                                        </td>

                                        <td>
                                            {{ $loan->sarpras?->name ?? '-' }}
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Tidak ada data barang keluar ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if($loans->isNotEmpty())
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">
                                            TOTAL BARANG KELUAR
                                        </th>

                                        <th class="text-center">
                                            {{ number_format($totalQuantity, 0, ',', '.') }}
                                        </th>

                                        <th colspan="2"></th>
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
                                Mardianto, S.Pd.
                            </p>

                            <p>NIP. -</p>
                        </div>

                        <div class="signature-box">
                            <p>
                                {{ config('app.school_city', 'Kabupaten/Kota') }},
                                {{ now()->translatedFormat('d F Y') }}
                            </p>

                            <p>Petugas Sarana dan Prasarana</p>

                            <div class="signature-space"></div>

                            <p class="signature-name">
                                {{ $loans->first()?->sarpras?->name ?? 'Nama Petugas' }}
                            </p>

                            <p>NIP. -</p>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
