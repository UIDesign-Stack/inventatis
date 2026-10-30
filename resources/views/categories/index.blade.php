
@extends('layouts.app')

@section('content')

<div class="inv-app">
    <div class="inv-main">
        <div class="container">

            {{-- HEADER HALAMAN --}}
            <div class="inv-page-head">
                <div>
                    <h1 class="h3 fw-bold mb-1">Kategori Barang</h1>
                    <p class="inv-page-sub mb-0">
                        Kelola kategori barang inventaris sekolah.
                    </p>
                </div>

                <div>
                    <a href="{{ route('categories.create') }}"
                        class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        Tambah Kategori
                    </a>
                </div>
            </div>

            {{-- NOTIFIKASI --}}
            @if(session('success'))
                <div class="alert alert-success inv-alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- DAFTAR KATEGORI --}}
            <div class="card inv-panel">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1 fw-semibold">Daftar Kategori</h5>
                        <small class="text-muted">
                            Total {{ $categories->count() }} kategori terdaftar
                        </small>
                    </div>

                    <span class="inv-pill">
                        {{ $categories->count() }} Kategori
                    </span>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover inv-table align-middle mb-0">

                            <thead>
                                <tr>
                                    <th width="6%">No</th>
                                    <th width="25%">Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th width="18%" class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($categories as $categorie)
                                    <tr>
                                        <td class="text-muted">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>
                                            <div class="fw-semibold">
                                                {{ $categorie->name }}
                                            </div>
                                        </td>

                                        <td>
                                            {{ $categorie->description ?: '-' }}
                                        </td>

                                        <td>
                                            <div class="inv-actions justify-content-center">

                                                {{-- EDIT --}}
                                                <a href="{{ route('categories.edit', $categorie->id) }}"
                                                    class="inv-icon-btn"
                                                    title="Edit Kategori">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                {{-- HAPUS --}}
                                                <form action="{{ route('categories.destroy', $categorie->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah kamu yakin ingin menghapus kategori ini?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="inv-icon-btn text-danger"
                                                        title="Hapus Kategori">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="inv-empty text-center py-5">
                                                <i class="bi bi-folder2-open fs-1 d-block mb-3"></i>
                                                <h6 class="fw-semibold">
                                                    Belum Ada Kategori
                                                </h6>
                                                <p class="text-muted mb-3">
                                                    Silakan tambahkan kategori barang terlebih dahulu.
                                                </p>

                                                <a href="{{ route('categories.create') }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="bi bi-plus-lg me-1"></i>
                                                    Tambah Kategori
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="card-footer inv-panel-foot d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $categories->count() }} kategori
                    </small>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection
