@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-6">

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <div class="rounded bg-green-100 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded bg-red-100 text-red-800 px-4 py-3">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded bg-red-100 text-red-800 px-4 py-3">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Detail Peminjaman #{{ $loan->id }}</h1>
        @php
            $badge = match ($loan->status) {
                'approved' => 'bg-blue-100 text-blue-800',
                'rejected' => 'bg-red-100 text-red-800',
                'handed_over' => 'bg-green-100 text-green-800',
                default => 'bg-yellow-100 text-yellow-800',
            };
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badge }}">
            {{ $statuses[$loan->status] ?? $loan->status }}
        </span>
    </div>

    {{-- Data peminjaman --}}
    <div class="bg-white shadow rounded p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm text-gray-500">Guru</dt>
                <dd class="font-medium">{{ $loan->teacher->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Barang</dt>
                <dd class="font-medium">{{ $loan->item->name }} ({{ $loan->item->code }})</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Jumlah diajukan</dt>
                <dd class="font-medium">{{ $loan->quantity }} {{ $loan->item->unit }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Stok saat ini</dt>
                <dd class="font-medium">{{ $loan->item->stock }} {{ $loan->item->unit }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Tanggal pinjam</dt>
                <dd class="font-medium">{{ $loan->loan_date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Dicatat oleh</dt>
                <dd class="font-medium">{{ $loan->creator?->name ?? '-' }}</dd>
            </div>

            @if ($loan->decided_at)
                <div>
                    <dt class="text-sm text-gray-500">Keputusan oleh</dt>
                    <dd class="font-medium">{{ $loan->decider?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Waktu keputusan</dt>
                    <dd class="font-medium">{{ $loan->decided_at->format('d M Y H:i') }}</dd>
                </div>
            @endif

            @if ($loan->status_note)
                <div class="sm:col-span-2">
                    <dt class="text-sm text-gray-500">Catatan</dt>
                    <dd class="font-medium">{{ $loan->status_note }}</dd>
                </div>
            @endif

            @if ($loan->status === 'handed_over')
                <div>
                    <dt class="text-sm text-gray-500">No. surat serah terima</dt>
                    <dd class="font-medium">{{ $loan->handover_letter_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Diserahkan oleh</dt>
                    <dd class="font-medium">{{ $loan->sarpras?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Waktu serah terima</dt>
                    <dd class="font-medium">{{ $loan->handed_over_at?->format('d M Y H:i') }}</dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Keputusan: hanya untuk yang berstatus pending --}}
    @can('loan.approve')
        @if ($loan->isPending())
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-lg font-semibold mb-4">Keputusan</h2>
                <form method="POST" action="{{ route('loans.decide', $loan) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="decision" value="approved" required>
                            <span>Setujui (ACC)</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="decision" value="rejected">
                            <span>Tolak</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="decision" value="pending">
                            <span>Tunda (pending) dengan catatan</span>
                        </label>
                    </div>

                    <div>
                        <label for="status_note" class="block text-sm text-gray-600 mb-1">
                            Alasan (wajib jika ditolak atau di-pending)
                        </label>
                        <textarea id="status_note" name="status_note" rows="3" maxlength="500"
                                  class="w-full border rounded px-3 py-2">{{ old('status_note') }}</textarea>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Simpan Keputusan
                    </button>
                </form>
            </div>
        @endif
    @endcan

    {{-- Serah terima: hanya untuk yang sudah disetujui --}}
    @can('loan.handover')
        @if ($loan->isApproved())
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-lg font-semibold mb-4">Serah Terima Barang</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Nomor surat dibuat otomatis. Stok akan dikurangi sebanyak
                    {{ $loan->quantity }} {{ $loan->item->unit }} setelah serah terima dicatat.
                </p>
                <form method="POST" action="{{ route('loans.handover', $loan) }}"
                      onsubmit="return confirm('Catat serah terima dan kurangi stok?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Catat Serah Terima
                    </button>
                </form>
            </div>
        @endif
    @endcan

    {{-- Aksi bawah --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('loans.index') }}" class="text-blue-600 hover:underline">&larr; Kembali ke daftar</a>

        @can('loan.create')
            @if (in_array($loan->status, ['pending', 'rejected'], true))
                <form method="POST" action="{{ route('loans.destroy', $loan) }}"
                      onsubmit="return confirm('Hapus data peminjaman ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                </form>
            @endif
        @endcan
    </div>
</div>
@endsection
