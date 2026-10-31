@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;

    $user = Auth::user();
    $role = ucfirst($user->getRoleNames()->first() ?? 'Pengguna');
    $hour = now()->hour;
    $salam = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $tanggal = Carbon::now()->locale('id')->translatedFormat('l, j F Y');

    // Akses cepat: hanya tampil jika route ada dan user punya permission-nya
    $shortcuts = [
        ['Barang',         'items.index',      'item.manage',     'Kelola data dan kondisi barang sekolah.',  'M21 8 12 3 3 8v8l9 5 9-5V8ZM3 8l9 5 9-5M12 13v8'],
        ['Kategori',       'categories.index', 'category.manage', 'Atur pengelompokan barang.',               'M4 6h16M4 12h16M4 18h10'],
        ['Guru',           'teachers.index',   'teacher.manage',  'Data guru peminjam dan penanggung jawab.', 'M9 11a3.2 3.2 0 1 0 0-6.4A3.2 3.2 0 0 0 9 11ZM3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M17 11a3 3 0 1 0 0-6M21 20c0-2.6-1.6-4.8-4-5.6'],
        ['Supplier',       'suppliers.index',  'supplier.manage', 'Daftar penyedia barang.',                  'M3 7h11v9H3zM14 10h4l3 3v3h-7M7 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM17 19a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z'],
        ['Peminjaman',     'loans.index',      'loan.view',       'Catat dan putuskan pengajuan peminjaman.', 'M8 7h12M8 12h12M8 17h12M3 7l1 1 2-2M3 12l1 1 2-2M3 17l1 1 2-2'],
        ['Pengembalian',   'loans.returns',    'loan.view',       'Catat barang yang dikembalikan guru.',     'M9 14 4 9l5-5M4 9h10a6 6 0 0 1 0 12h-3'],
        ['User',           'users.index',      'user.view',       'Kelola akun dan hak akses.',               'M12 3 3 7v5c0 5 3.8 8.4 9 9 5.2-.6 9-4 9-9V7l-9-4ZM9 12l2 2 4-4'],
        ['Barang Masuk',   'items.report',     'stock-in.report',  'Laporan barang yang masuk.',              'M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5ZM14 3v5h5M9 13h6M9 17h6'],
        ['Barang Keluar',  'loans.report',     'stock-out.report', 'Laporan barang yang keluar.',             'M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5ZM14 3v5h5M9 13h6M9 17h6'],
    ];

    $visibleShortcuts = collect($shortcuts)->filter(fn ($s) => Route::has($s[1]) && $user->can($s[2]));
@endphp

<div class="container">

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    {{-- Sapaan --}}
    <section class="inv-hello d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <p class="mb-1 inv-hello-date">{{ $tanggal }}</p>
            <h1 class="fs-2 mb-1">{{ $salam }}, {{ $user->name }}</h1>
            <p class="mb-0 inv-hello-sub">Anda masuk sebagai {{ $role }}. Berikut ringkasan pekerjaan Anda hari ini.</p>
        </div>
        <div class="inv-barcode d-none d-md-block" aria-hidden="true"></div>
    </section>

    {{-- ===================== SARPRAS: PEMINJAMAN ===================== --}}
    @if ($loans)
        <h2 class="fs-5 mt-4 mb-3">Peminjaman</h2>

        <section class="row g-3" aria-label="Ringkasan peminjaman">
            @foreach ($loans['stats'] as $stat)
                <div class="col-6 col-lg-3">
                    <div class="inv-stat">
                        <div class="inv-stat-label">{{ $stat['label'] }}</div>
                        <div class="inv-stat-value font-display">{{ number_format($stat['value'], 0, ',', '.') }}</div>
                        <div class="inv-stat-note">{{ $stat['note'] }}</div>
                    </div>
                </div>
            @endforeach
        </section>

        <div class="row g-3 mt-1">
            {{-- Pengajuan menunggu keputusan --}}
            <div class="col-lg-6">
                <div class="card inv-panel h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>Menunggu keputusan</span>
                        @can('loan.view')
                            <a href="{{ route('loans.index', ['status' => 'pending']) }}" class="small">Lihat semua</a>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 inv-table">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>Barang</th>
                                    <th>Tanggal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($loans['pending'] as $loan)
                                    <tr>
                                        <td>{{ $loan->teacher->name ?? '-' }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $loan->item->name ?? '-' }}</div>
                                            <div class="small text-muted">{{ $loan->quantity }} {{ $loan->item->unit ?? '' }}</div>
                                        </td>
                                        <td class="inv-num text-nowrap">{{ $loan->loan_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">Tinjau</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Tidak ada pengajuan yang menunggu keputusan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Barang yang masih dipinjam --}}
            <div class="col-lg-6">
                <div class="card inv-panel h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>Sedang dipinjam</span>
                        @if (Route::has('loans.returns'))
                            <a href="{{ route('loans.returns') }}" class="small">Catat pengembalian</a>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 inv-table">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>Barang</th>
                                    <th>Lama pinjam</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($loans['borrowed'] as $loan)
                                    @php
                                        $since = $loan->handed_over_at ?? $loan->decided_at ?? $loan->loan_date;
                                        $days = $since
                                            ? (int) abs($since->copy()->startOfDay()->diffInDays(now()->startOfDay()))
                                            : null;
                                    @endphp
                                    <tr>
                                        <td>{{ $loan->teacher->name ?? '-' }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $loan->item->name ?? '-' }}</div>
                                            <div class="small text-muted">{{ $loan->quantity }} {{ $loan->item->unit ?? '' }}</div>
                                        </td>
                                        <td class="inv-num text-nowrap">
                                            @if ($days === null)
                                                -
                                            @elseif ($days === 0)
                                                Hari ini
                                            @else
                                                {{ $days }} hari
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Tidak ada barang yang sedang dipinjam.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ===================== OPERATOR: STOK BARANG ===================== --}}
    @if ($inventory)
        <h2 class="fs-5 mt-5 mb-3">Stok barang</h2>

        <section class="row g-3" aria-label="Ringkasan stok">
            @foreach ($inventory['stats'] as $stat)
                <div class="col-6 col-lg">
                    <div class="inv-stat">
                        <div class="inv-stat-label">{{ $stat['label'] }}</div>
                        <div class="inv-stat-value font-display">{{ number_format($stat['value'], 0, ',', '.') }}</div>
                        <div class="inv-stat-note">{{ $stat['note'] }}</div>
                    </div>
                </div>
            @endforeach
        </section>

        <div class="row g-3 mt-1">
            {{-- Barang masuk terbaru --}}
            <div class="col-lg-6">
                <div class="card inv-panel h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>Barang masuk terbaru</span>
                        <a href="{{ route('items.index') }}" class="small">Lihat semua</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 inv-table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inventory['latest'] as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('items.show', $item) }}" class="fw-semibold text-decoration-none">{{ $item->name }}</a>
                                            <div class="inv-code">{{ $item->code }}</div>
                                        </td>
                                        <td>{{ $item->category->name ?? '-' }}</td>
                                        <td class="inv-num text-nowrap">{{ $item->stock }} {{ $item->unit }}</td>
                                        <td class="inv-num text-nowrap">{{ $item->received_date?->format('d/m/Y') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Belum ada data barang masuk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Stok menipis --}}
            <div class="col-lg-6">
                <div class="card inv-panel h-100">
                    <div class="card-header">
                        Stok menipis
                        <span class="text-muted small">(stok {{ $inventory['threshold'] }} atau kurang)</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 inv-table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Kategori</th>
                                    <th>Sisa stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inventory['low'] as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('items.show', $item) }}" class="fw-semibold text-decoration-none">{{ $item->name }}</a>
                                            <div class="inv-code">{{ $item->code }}</div>
                                        </td>
                                        <td>{{ $item->category->name ?? '-' }}</td>
                                        <td class="inv-num text-nowrap">
                                            <span class="inv-pill {{ $item->stock === 0 ? 'inv-pill--bad' : 'inv-pill--warn' }}">
                                                {{ $item->stock }} {{ $item->unit }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Semua stok masih aman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ===================== AKSES CEPAT ===================== --}}
    <h2 class="fs-5 mt-5 mb-3">Akses cepat</h2>

    @if ($visibleShortcuts->isNotEmpty())
        <section class="row g-3 mb-4">
            @foreach ($visibleShortcuts as [$label, $route, $can, $desc, $icon])
                <div class="col-sm-6 col-lg-4">
                    <a href="{{ route($route) }}" class="inv-quick">
                        <span class="inv-quick-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                        </span>
                        <span>
                            <span class="d-block fw-semibold">{{ $label }}</span>
                            <span class="d-block small inv-muted">{{ $desc }}</span>
                        </span>
                    </a>
                </div>
            @endforeach
        </section>
    @else
        <div class="card">
            <div class="card-body">
                <p class="fw-semibold mb-1">Akun Anda belum punya akses ke menu apa pun.</p>
                <p class="inv-muted mb-0">Minta admin sekolah untuk mengatur hak akses akun Anda.</p>
            </div>
        </div>
    @endif

</div>
@endsection
