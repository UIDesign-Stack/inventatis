@extends('layouts.app')

@section('content')
<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('users.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Daftar user
    </a>

    <div class="inv-page-head">
        <div class="d-flex align-items-center gap-3">
            <span class="inv-avatar" style="width: 48px; height: 48px; font-size: 1.15rem;" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            <div>
                <h1 class="h3 font-display fw-bold mb-1">
                    {{ $user->name }}
                    @if ($user->id === auth()->id())
                        <span class="inv-pill inv-pill--brand ms-1 align-middle">Anda</span>
                    @endif
                </h1>
                <p class="inv-page-sub mb-0">{{ $user->email }}</p>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @can('user.update')
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit user</a>
            @endcan

            @can('user.delete')
                @if ($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm(@js('Hapus user '.$user->name.'?'))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Hapus</button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    {{-- Rincian --}}
    <div class="card inv-panel">
        <section class="inv-form-section">
            <h2 class="inv-form-title">Informasi akun</h2>

            <dl class="row mb-0 gy-3">
                <dt class="col-sm-4 fw-normal text-muted">Nama</dt>
                <dd class="col-sm-8 mb-0">{{ $user->name }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Email</dt>
                <dd class="col-sm-8 mb-0">{{ $user->email }}</dd>

                <dt class="col-sm-4 fw-normal text-muted">Role</dt>
                <dd class="col-sm-8 mb-0">
                    @forelse ($user->roles as $role)
                        <span class="inv-role">{{ ucfirst($role->name) }}</span>
                    @empty
                        <span class="text-muted">-</span>
                    @endforelse
                </dd>

                <dt class="col-sm-4 fw-normal text-muted">Terdaftar</dt>
                <dd class="col-sm-8 mb-0 inv-num">{{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>
            </dl>
        </section>

        <section class="inv-form-section border-bottom-0">
            <h2 class="inv-form-title">Hak akses</h2>

            @forelse ($permissions as $permission)
                @if ($loop->first)
                    <div class="d-flex flex-wrap gap-2">
                @endif

                <span class="inv-role">{{ $permission->name }}</span>

                @if ($loop->last)
                    </div>
                @endif
            @empty
                <p class="text-muted mb-0">Tidak ada hak akses.</p>
            @endforelse
        </section>
    </div>
</div>
@endsection
