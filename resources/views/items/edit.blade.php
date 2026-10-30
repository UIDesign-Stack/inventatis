@extends('layouts.app')

@section('content')
<div class="container inv-form-wrap">

    {{-- Header halaman --}}
    <a href="{{ route('items.index') }}" class="inv-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Data barang
    </a>

    <div class="mb-4">
        <h1 class="h3 font-display fw-bold mb-1">Edit barang</h1>
        <p class="inv-page-sub mb-0">
            {{ $item->name }} <span class="inv-code">({{ $item->code }})</span>.
            Kolom bertanda <span class="text-danger">*</span> wajib diisi.
        </p>
    </div>

    <form action="{{ route('items.update', $item) }}" method="POST" class="card inv-panel">
        @csrf
        @method('PUT')

        {{-- Data barang --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Data barang</h2>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="code" class="form-label">Kode barang <span class="text-danger">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code', $item->code) }}"
                           class="form-control @error('code') is-invalid @enderror" required disabled>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-8">
                    <label for="name" class="form-label">Nama barang <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select id="category_id" name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                    @selected(old('category_id', $item->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Stok --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Stok dan kondisi</h2>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="stock" class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $item->stock) }}"
                           class="form-control @error('stock') is-invalid @enderror" required>
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="unit" class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $item->unit) }}"
                           class="form-control @error('unit') is-invalid @enderror" required>
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="condition" class="form-label">Kondisi <span class="text-danger">*</span></label>
                    <select id="condition" name="condition"
                            class="form-select @error('condition') is-invalid @enderror" required>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition }}"
                                    @selected(old('condition', $item->condition) === $condition)>
                                {{ ucfirst($condition) }}
                            </option>
                        @endforeach
                    </select>
                    @error('condition')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Penerimaan --}}
        <section class="inv-form-section">
            <h2 class="inv-form-title">Penerimaan</h2>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select id="supplier_id" name="supplier_id"
                            class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">Pilih supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                    @selected(old('supplier_id', $item->supplier_id) == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="received_date" class="form-label">Tanggal terima <span class="text-danger">*</span></label>
                    <input type="date" id="received_date" name="received_date"
                           value="{{ old('received_date', $item->received_date->format('Y-m-d')) }}"
                           class="form-control @error('received_date') is-invalid @enderror" required>
                    @error('received_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Keterangan</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Aksi --}}
        <div class="inv-form-foot">
            <a href="{{ route('items.index') }}" class="btn btn-outline-primary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
        </div>
    </form>
</div>
@endsection
