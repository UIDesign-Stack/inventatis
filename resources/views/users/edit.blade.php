@extends('layouts.app')

@section('content')
@php
    $currentRole = $user->roles->first()?->name;
    $isSelf = $user->id === auth()->id();
@endphp

<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('users.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Daftar user
    </a>

    <div class="mb-4">
        <h1 class="h3 font-display fw-bold mb-1">Edit user</h1>
        <p class="inv-page-sub mb-0">
            {{ $user->name }}
            @if ($isSelf)
                <span class="inv-pill inv-pill--brand ms-1">Anda</span>
            @endif
        </p>
    </div>

    @if ($errors->any())
        <div class="alert inv-alert inv-alert--danger" role="alert">
            <div class="fw-semibold mb-1">Perubahan belum bisa disimpan:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST" class="card inv-panel">
        @csrf
        @method('PUT')

        {{-- Akun --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Data akun</h2>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>

                    @if ($isSelf)
                        {{-- Role sendiri tidak bisa diubah; nilai tetap dikirim lewat hidden input --}}
                        <select id="role" class="form-select" disabled>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected($currentRole === $role->name)>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="role" value="{{ $currentRole }}">
                        <div class="form-text">Role akun Anda sendiri tidak bisa diubah.</div>
                    @else
                        <select id="role" name="role"
                                class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Pilih role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role', $currentRole) === $role->name)>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="inv-form-foot">
            <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        </div>
    </form>
</div>
@endsection
