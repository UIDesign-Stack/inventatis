@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header halaman --}}
    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">Data guru</h1>
            <p class="inv-page-sub mb-0">{{ number_format($teachers->total()) }} guru terdaftar</p>
        </div>
        <div>
            <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Tambah guru
            </a>
        </div>
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert inv-alert inv-alert--success" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert inv-alert inv-alert--danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card inv-panel">

        {{-- Pencarian --}}
        <form method="GET" action="{{ route('teachers.index') }}" class="inv-toolbar">
            <div class="inv-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama, NIP, atau mapel"
                       aria-label="Cari nama, NIP, atau mapel">
            </div>

            <div class="inv-toolbar-actions d-flex gap-2">
                <button type="submit" class="btn btn-primary">Cari</button>
                @if (request()->filled('search'))
                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-primary">Reset</a>
                @endif
            </div>
        </form>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Guru</th>
                        <th>Telepon</th>
                        <th>Mapel / jabatan</th>
                        <th class="text-end">Jumlah pinjaman</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td class="ps-4 text-muted">{{ $teachers->firstItem() + $loop->index }}</td>
                            <td>
                                <a href="{{ route('teachers.show', $teacher) }}" class="inv-name">{{ $teacher->name }}</a>
                                <div class="inv-code">NIP {{ $teacher->nip ?? '-' }}</div>
                            </td>
                            <td class="inv-num text-nowrap">{{ $teacher->phone ?? '-' }}</td>
                            <td>{{ $teacher->subject ?? '-' }}</td>
                            <td class="text-end inv-num">
                                <span class="fw-semibold">{{ $teacher->loans_count }}</span>
                            </td>
                            <td class="pe-4">
                                <div class="inv-actions justify-content-end">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="inv-icon-btn"
                                       title="Detail" aria-label="Detail {{ $teacher->name }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>

                                    <a href="{{ route('teachers.edit', $teacher) }}" class="inv-icon-btn"
                                       title="Edit" aria-label="Edit {{ $teacher->name }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    </a>

                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm(@js('Hapus data guru '.$teacher->name.'?'))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inv-icon-btn inv-icon-btn--danger"
                                                title="Hapus" aria-label="Hapus {{ $teacher->name }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m6 6 1 14h10l1-14"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="inv-empty">
                                    @if (request()->filled('search'))
                                        <p class="fw-semibold mb-1">Tidak ada guru yang cocok</p>
                                        <p class="text-muted mb-3">Coba nama, NIP, atau mapel yang berbeda.</p>
                                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-primary">Reset pencarian</a>
                                    @else
                                        <p class="fw-semibold mb-1">Belum ada data guru</p>
                                        <p class="text-muted mb-3">Tambahkan guru untuk mulai mencatat peminjaman.</p>
                                        <a href="{{ route('teachers.create') }}" class="btn btn-primary">Tambah guru</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info jumlah + paginasi --}}
        @if ($teachers->total() > 0)
            <div class="inv-panel-foot">
                <span class="text-muted small">
                    Menampilkan {{ $teachers->firstItem() }}–{{ $teachers->lastItem() }} dari {{ $teachers->total() }} guru
                </span>
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
