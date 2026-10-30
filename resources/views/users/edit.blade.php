@extends('layouts.app')

@section('content')
    <h1>Edit User</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @php
        $currentRole = $user->roles->first()?->name;
        $isSelf = $user->id === auth()->id();
    @endphp

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Nama</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <br>

        <div>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <br>

        <div>
            <label for="role">Role</label><br>

            @if ($isSelf)
                {{-- Role sendiri tidak bisa diubah; nilai tetap dikirim lewat hidden input --}}
                <select id="role" disabled>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @selected($currentRole === $role->name)>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="role" value="{{ $currentRole }}">
                <br>
                <small>Role akun Anda sendiri tidak bisa diubah.</small>
            @else
                <select id="role" name="role" required>
                    <option value="">-- Pilih role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @selected(old('role', $currentRole) === $role->name)>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <br>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('users.index') }}">Batal</a>
    </form>
@endsection
