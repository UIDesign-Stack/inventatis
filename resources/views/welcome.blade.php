<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Inventaris Sekolah') }} | Inventaris Barang Sekolah</title>
    <meta name="description" content="Catat, pantau, dan pinjamkan semua barang sekolah dalam satu sistem.">

    <!-- Mode terang/gelap mengikuti pengaturan perangkat (dipasang sebelum CSS agar tidak berkedip) -->
    <script>
        document.documentElement.setAttribute(
            'data-bs-theme',
            window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        );
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="inv-app">

    {{-- Navigasi --}}
    <header class="container py-3 py-lg-4">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-3 text-decoration-none text-reset">
                <div class="school-logo">
                    <img src="{{ asset('images/logo.png') }}"
                        alt="Logo Sekolah">
                </div>
                <span class="font-display fs-5 fw-bold">{{ config('app.name', 'Inventaris Sekolah') }}</span>
            </a>

            @if (Route::has('login'))
                <nav class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-brand">Buka dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-link text-reset">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline-inv">Daftar</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <main>
        {{-- Hero --}}
        <section class="container pt-4 pb-5 pt-lg-5 pb-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="inv-pill">Sistem inventaris untuk sekolah</span>

                    <h1 class="font-display inv-title mt-4 mb-3">
                        Semua barang sekolah tercatat rapi, dari proyektor sampai bola voli.
                    </h1>

                    <p class="inv-lead inv-muted">
                        Catat barang, tempel kode, pantau kondisinya, dan kelola peminjaman oleh guru dan siswa. Tidak perlu lagi buku besar dan tumpukan kertas.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-brand btn-lg">Buka dashboard</a>
                        @else
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="btn btn-brand btn-lg">Masuk ke sistem</a>
                            @endif
                            <a href="#fitur" class="btn btn-outline-inv btn-lg">Lihat fitur</a>
                        @endauth
                    </div>

                    <dl class="d-flex flex-wrap gap-4 mt-5 mb-0">
                        <div><dt class="fw-normal small inv-muted">Pencatatan</dt><dd class="font-display fs-5 fw-bold mb-0">Real-time</dd></div>
                        <div><dt class="fw-normal small inv-muted">Akses</dt><dd class="font-display fs-5 fw-bold mb-0">Operator,Sarpras</dd></div>
                        <div><dt class="fw-normal small inv-muted">Laporan</dt><dd class="font-display fs-5 fw-bold mb-0">PDF</dd></div>
                    </dl>
                </div>

                {{-- Kartu pratinjau gaya label aset --}}
                <div class="col-lg-6">
                    <div class="inv-ticket mx-auto" style="max-width: 32rem;">
                        <div class="d-flex align-items-end justify-content-between">
                            <div>
                                <p class="small inv-muted mb-0">Ruang Laboratorium Komputer</p>
                                <h2 class="font-display fs-5 fw-bold mb-0">Daftar barang</h2>
                            </div>
                            <span class="inv-count">48 item</span>
                        </div>

                        @php
                            $items = [
                                ['Komputer PC-01', 'INV/LK/001', 'Baik', 'ok'],
                                ['Proyektor Epson EB-X06', 'INV/LK/014', 'Dipinjam', 'borrowed'],
                                ['Switch TP-Link 24 port', 'INV/LK/022', 'Baik', 'ok'],
                                ['Printer Canon G2010', 'INV/LK/031', 'Perlu servis', 'service'],
                                ['Kursi lab', 'INV/LK/045', 'Rusak ringan', 'broken'],
                            ];
                        @endphp

                        <ul class="list-unstyled mt-3 mb-0">
                            @foreach ($items as [$nama, $kode, $status, $kelas])
                                <li class="inv-row">
                                    <div class="text-truncate">
                                        <div class="fw-medium text-truncate">{{ $nama }}</div>
                                        <div class="inv-code">{{ $kode }}</div>
                                    </div>
                                    <span class="inv-status inv-status-{{ $kelas }}">{{ $status }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="d-flex align-items-center justify-content-between gap-3 border-top inv-line pt-3 mt-3">
                            <div class="inv-barcode"></div>
                            <span class="inv-code">INV/LK/001</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Fitur --}}
        <section id="fitur" class="inv-section-alt">
            <div class="container py-5 py-lg-5">
                <h2 class="font-display fw-bold display-6 mb-2" style="max-width: 36rem;">Yang bisa dikerjakan di sistem ini</h2>
                <p class="inv-muted mb-5" style="max-width: 32rem;">Dibuat untuk pekerjaan harian petugas sarana prasarana, guru, dan wali kelas.</p>

                <div class="row g-3">
                    {{-- Fitur utama --}}
                    <div class="col-lg-8">
                        <article class="inv-feature-main h-100">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v16"/></svg>
                            <h3 class="font-display fs-3 fw-bold mt-4">Data barang lengkap</h3>
                            <p>Simpan nama, merek, tahun pengadaan, sumber dana (BOS, komite, hibah), harga, foto, dan lokasi ruangan di satu tempat. Cari barang apa pun dalam hitungan detik.</p>
                            <div class="d-flex flex-wrap gap-2 mt-4">
                                @foreach (['Kategori', 'Barang', 'Supplier', 'Peminjaman dan Pengembalian', 'Laporan'] as $chip)
                                    <span class="inv-chip">{{ $chip }}</span>
                                @endforeach
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4 d-flex flex-column gap-3">
                        <article class="inv-feature flex-fill">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h3v3H7zM14 7h3v3h-3zM7 14h3v3H7zM14 14h3M17 14v3M14 17h1"/><path d="M4 8V5a1 1 0 0 1 1-1h3M20 8V5a1 1 0 0 0-1-1h-3M4 16v3a1 1 0 0 0 1 1h3M20 16v3a1 1 0 0 1-1 1h-3"/></svg>
                            <h3 class="font-display fs-5 fw-bold mt-3">Kode QR per barang</h3>
                            <p>Cetak dan tempel label. Pindai untuk melihat riwayat barang.</p>
                        </article>
                        <article class="inv-feature flex-fill">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 7h12M8 12h12M8 17h12"/><path d="m3 7 1 1 2-2M3 12l1 1 2-2M3 17l1 1 2-2"/></svg>
                            <h3 class="font-display fs-5 fw-bold mt-3">Peminjaman dan pengembalian</h3>
                            <p>Siapa meminjam, kapan kembali, dan kondisinya saat dikembalikan.</p>
                        </article>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <article class="inv-feature h-100">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 3 7v5c0 5 3.8 8.4 9 9 5.2-.6 9-4 9-9V7l-9-4Z"/><path d="m9 12 2 2 4-4"/></svg>
                            <h3 class="font-display fs-5 fw-bold mt-3">Pantau kondisi</h3>
                            <p>Tandai barang baik, perlu servis, atau rusak. Tidak ada yang terlewat.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <article class="inv-feature h-100">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg>
                            <h3 class="font-display fs-5 fw-bold mt-3">Laporan siap cetak</h3>
                            <p>Unduh rekap aset untuk audit, akreditasi, dan laporan BOS.</p>
                        </article>
                    </div>
                    <div class="col-lg-4">
                        <article class="inv-feature h-100">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M17 11a3 3 0 1 0 0-6M21 20c0-2.6-1.6-4.8-4-5.6"/></svg>
                            <h3 class="font-display fs-5 fw-bold mt-3">Hak akses sesuai peran</h3>
                            <p>Admin mengelola seluruh data, guru mengajukan peminjaman, siswa melihat barang yang boleh dipinjam.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        {{-- Alur --}}
        <section class="container py-5 py-lg-5">
            <h2 class="font-display fw-bold display-6 mb-5" style="max-width: 32rem;">Mulai dalam empat langkah</h2>

            <ol class="list-unstyled row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-0">
                @foreach ([
                    ['Catat barang', 'Input data barang satu per satu atau impor dari Excel.'],
                    ['Tempel kode', 'Cetak label QR dan tempel di setiap barang.'],
                    ['Kelola pemakaian', 'Catat peminjaman, pengembalian, dan perubahan kondisi.'],
                    ['Unduh laporan', 'Ambil rekap aset kapan pun dibutuhkan.'],
                ] as $i => [$judul, $isi])
                    <li class="col">
                        <div class="inv-step">
                            <span class="inv-step-no">{{ $i + 1 }}</span>
                            <h3 class="font-display fs-5 fw-bold mt-3 mb-1">{{ $judul }}</h3>
                            <p>{{ $isi }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>

        {{-- Ajakan akhir --}}
        <section class="container pb-5 pb-lg-5">
            <div class="inv-cta d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-4">
                <div>
                    <h2 class="font-display fs-2 fw-bold mb-2">Siap merapikan inventaris sekolah?</h2>
                    <p class="mb-0">Masuk dengan akun yang diberikan oleh admin sekolah.</p>
                </div>
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-white-inv btn-lg flex-shrink-0">Buka dashboard</a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn btn-tag btn-lg flex-shrink-0">Masuk sekarang</a>
                    @endif
                @endauth
            </div>
        </section>
    </main>

    <footer class="inv-footer">
        <div class="container py-4 d-flex flex-column flex-sm-row justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Inventaris Sekolah') }} | Baihaqi Hakim.</span>
        </div>
    </footer>

</body>
</html>
