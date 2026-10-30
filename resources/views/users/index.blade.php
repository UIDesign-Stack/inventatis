@extends('layouts.app')

@section('content')
    <h1>Daftar User</h1>

    @can('user.create')
        <a href="{{ route('users.create') }}">+ Tambah User</a>
        <br><br>
    @endcan

    {{-- Pesan sukses / error --}}
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    {{-- Pencarian & filter role --}}
    <form method="GET" action="{{ route('users.index') }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email">

        <select name="role">
            <option value="">Semua role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @selected(request('role') === $role->name)>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>

        <button type="submit">Cari</button>
        <a href="{{ route('users.index') }}">Reset</a>
    </form>

    <br>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Terdaftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $users->firstItem() + $loop->index }}</td>
                    <td>
                        {{ $user->name }}
                        @if ($user->id === auth()->id())
                            (Anda)
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @forelse ($user->roles as $role)
                            {{ ucfirst($role->name) }}@if (! $loop->last), @endif
                        @empty
                            -
                        @endforelse
                    </td>
                    <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                    <td>
                        @can('user.view')
                            <a href="{{ route('users.show', $user) }}">Detail</a>
                        @endcan

                        @can('user.update')
                            <a href="{{ route('users.edit', $user) }}">Edit</a>
                        @endcan

                        @can('user.delete')
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;"
                                      onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Hapus</button>
                                </form>
                            @endif
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>

    {{ $users->links() }}
@endsection
