@extends('layouts.app')

@section('content')
    <h1>Detail User</h1>

    <table cellpadding="6" cellspacing="0">
        <tr>
            <th align="left">Nama</th>
            <td>
                {{ $user->name }}
                @if ($user->id === auth()->id())
                    (Anda)
                @endif
            </td>
        </tr>
        <tr>
            <th align="left">Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th align="left">Role</th>
            <td>
                @forelse ($user->roles as $role)
                    {{ ucfirst($role->name) }}@if (! $loop->last), @endif
                @empty
                    -
                @endforelse
            </td>
        </tr>
        <tr>
            <th align="left">Terdaftar</th>
            <td>{{ $user->created_at?->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th align="left" valign="top">Hak akses</th>
            <td>
                @forelse ($permissions as $permission)
                    {{ $permission->name }}<br>
                @empty
                    -
                @endforelse
            </td>
        </tr>
    </table>

    <br>

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

    <a href="{{ route('users.index') }}">Kembali</a>
@endsection
