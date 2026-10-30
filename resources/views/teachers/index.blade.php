@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Data Guru</h1>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary">+ Tambah Guru</a>
    </div>

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Pencarian --}}
    <form method="GET" action="{{ route('teachers.index') }}" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari nama / NIP / mapel">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Mapel / Jabatan</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $teacher)
                    <tr>
                        <td>{{ $teachers->firstItem() + $loop->index }}</td>
                        <td>{{ $teacher->nip ?? '-' }}</td>
                        <td>{{ $teacher->name }}</td>
                        <td>{{ $teacher->phone ?? '-' }}</td>
                        <td>{{ $teacher->subject ?? '-' }}</td>
                        <td>{{ $teacher->loans_count }}</td>
                        <td>
                            <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-info">Detail</a>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-warning">Edit</a>

                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus data guru {{ $teacher->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $teachers->links() }}
</div>
@endsection
