@extends('layouts.app')

@section('content')
<div class="container">
    <div class="inv-form-wrap">
        <a href="{{ route('loans.index') }}" class="inv-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Data Peminjaman
        </a>

        <div class="inv-page-head">
            <div>
                <h1 class="h3 mb-1">Catat Peminjaman</h1>
                <div class="inv-page-sub">Isi data guru, barang, dan jumlah yang dipinjam.</div>
            </div>
        </div>

        <form action="{{ route('loans.store') }}" method="POST">
            @csrf

            <div class="card inv-panel">
                {{-- Peminjam dan barang --}}
                <div class="inv-form-section">
                    <h2 class="inv-form-title">Peminjam dan barang</h2>

                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Guru <span class="text-danger">*</span></label>
                        <select id="teacher_id" name="teacher_id"
                                class="form-select @error('teacher_id') is-invalid @enderror" required>
                            <option value="">-- Pilih guru --</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>
                                    {{ $teacher->name }}@if ($teacher->nip) ({{ $teacher->nip }})@endif
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @can('teacher.manage')
                            <div class="form-text">
                                Guru belum terdaftar?
                                <a href="{{ route('teachers.create') }}">Tambah guru baru</a>
                            </div>
                        @endcan
                    </div>

                    <div class="mb-0">
                        <label for="item_id" class="form-label">Barang <span class="text-danger">*</span></label>
                        <select id="item_id" name="item_id"
                                class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">-- Pilih barang --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                                    {{ $item->code }} - {{ $item->name }}
                                    (stok: {{ $item->stock }} {{ $item->unit }})
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Jumlah dan tanggal --}}
                <div class="inv-form-section">
                    <h2 class="inv-form-title">Jumlah dan tanggal</h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                                   class="form-control @error('quantity') is-invalid @enderror" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="loan_date" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" id="loan_date" name="loan_date"
                                   value="{{ old('loan_date', now()->format('Y-m-d')) }}"
                                   class="form-control @error('loan_date') is-invalid @enderror" required>
                            @error('loan_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <p class="text-muted small mt-3 mb-0">
                        Setelah dicatat, status awal peminjaman adalah <strong>Pending</strong>.
                        Keputusan ACC, tolak, atau pending diberikan di halaman detail.
                    </p>
                </div>

                <div class="inv-form-foot">
                    <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
