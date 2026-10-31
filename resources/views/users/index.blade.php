@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header halaman --}}
    <div class="inv-page-head">
        <div>
            <h1 class="h3 font-display fw-bold mb-1">Daftar user</h1>
            <p class="inv-page-sub mb-0">{{ number_format($users->total()) }} user terdaftar</p>
        </div>
        @can('user.create')
            <div>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah user
                </a>
            </div>
        @endcan
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert inv-alert inv-alert--success" role="status">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert inv-alert inv-alert--danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card inv-panel">

        {{-- Pencarian & filter role --}}
        <form method="GET" action="{{ route('users.index') }}" class="inv-toolbar">
            <div class="inv-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama atau email"
                       aria-label="Cari nama atau email">
            </div>

            <select name="role" class="form-select inv-filter" aria-label="Filter role">
                <option value="">Semua role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" @selected(request('role') === $role->name)>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>

            <div class="inv-toolbar-actions d-flex gap-2">
                <button type="submit" class="btn btn-primary">Cari</button>
                @if (request()->filled('search') || request()->filled('role'))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Reset</a>
                @endif
            </div>
        </form>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 inv-table">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="ps-4 text-muted">{{ $users->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="inv-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $user->name }}
                                            @if ($user->id === auth()->id())
                                                <span class="inv-pill inv-pill--brand ms-1">Anda</span>
                                            @endif
                                        </div>
                                        <div class="inv-code">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="inv-role">{{ ucfirst($role->name) }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="inv-num text-nowrap">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="pe-4">
                                <div class="inv-actions justify-content-end">
                                    @can('user.view')
                                        <a href="{{ route('users.show', $user) }}" class="inv-icon-btn"
                                           title="Detail" aria-label="Detail {{ $user->name }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                    @endcan

                                    @can('user.update')
                                        <a href="{{ route('users.edit', $user) }}" class="inv-icon-btn"
                                           title="Edit" aria-label="Edit {{ $user->name }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        </a>
                                    @endcan

                                    @can('user.delete')
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm(@js('Hapus user '.$user->name.'?'))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inv-icon-btn inv-icon-btn--danger"
                                                        title="Hapus" aria-label="Hapus {{ $user->name }}">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m6 6 1 14h10l1-14"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="inv-empty">
                                    @if (request()->filled('search') || request()->filled('role'))
                                        <p class="fw-semibold mb-1">Tidak ada user yang cocok</p>
                                        <p class="text-muted mb-3">Coba kata kunci lain atau pilih role yang berbeda.</p>
                                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Reset pencarian</a>
                                    @else
                                        <p class="fw-semibold mb-0">Tidak ada data user</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: info jumlah + paginasi --}}
        @if ($users->total() > 0)
            <div class="inv-panel-foot">
                <span class="text-muted small">
                    Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} user
                </span>
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
