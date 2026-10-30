@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Edit Guru</h1>

    <form action="{{ route('teachers.update', $teacher) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nip" class="form-label">NIP</label>
            <input type="text" id="nip" name="nip" value="{{ old('nip', $teacher->nip) }}"
                   class="form-control @error('nip') is-invalid @enderror">
            @error('nip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Boleh dikosongkan.</div>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $teacher->name) }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Telepon</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $teacher->phone) }}"
                   class="form-control @error('phone') is-invalid @enderror">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="subject" class="form-label">Mapel / Jabatan</label>
            <input type="text" id="subject" name="subject" value="{{ old('subject', $teacher->subject) }}"
                   class="form-control @error('subject') is-invalid @enderror">
            @error('subject')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div>
@endsection
