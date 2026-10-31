<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/_layout.scss', 'resources/js/app.js'])
</head>
<body class="inv-app">
    <div id="app">
        @php
            // Menu utama: tampil hanya jika route-nya ada dan user punya permission-nya
            $menus = [
                ['label' => 'Dashboard',  'route' => 'home',             'can' => [],                'active' => ['home']],
                ['label' => 'Barang',     'route' => 'items.index',      'can' => 'item.manage',     'active' => ['items.index', 'items.create', 'items.edit', 'items.show']],
                ['label' => 'Kategori',   'route' => 'categories.index', 'can' => 'category.manage', 'active' => ['categories.*']],
                ['label' => 'Guru',       'route' => 'teachers.index',   'can' => 'teacher.manage',  'active' => ['teachers.*']],
                ['label' => 'Supplier',   'route' => 'suppliers.index',  'can' => 'supplier.manage', 'active' => ['suppliers.*']],
                ['label' => 'Peminjaman',   'route' => 'loans.index',   'can' => 'loan.view', 'active' => ['loans.index', 'loans.create', 'loans.show']],
                ['label' => 'Pengembalian', 'route' => 'loans.returns', 'can' => ['loan.view', 'loan.create'], 'active' => ['loans.returns']],
                ['label' => 'User',       'route' => 'users.index',      'can' => 'user.view',       'active' => ['users.*']],
            ];

            // Menu laporan (dropdown)
            $reports = [
                ['label' => 'Barang Masuk',  'route' => 'items.report', 'can' => 'stock-in.report',  'active' => ['items.report']],
                ['label' => 'Barang Keluar', 'route' => 'loans.report', 'can' => 'stock-out.report', 'active' => ['loans.report']],
            ];

            $visible = fn (array $list) => auth()->check()
                ? collect($list)->filter(fn ($m) => Route::has($m['route'])
                    && collect((array) $m['can'])->every(fn ($p) => auth()->user()->can($p)))
                : collect();

            $visibleMenus = $visible($menus);
            $visibleReports = $visible($reports);
        @endphp

        <nav class="navbar navbar-expand-md inv-nav">
            <div class="container">
                <a class="inv-brand font-display fw-bold" href="{{ url('/') }}">
                    <div class="school-logo">
                            <img src="{{ asset('images/logo.png') }}"
                                alt="Logo Sekolah">
                        </div>
                    {{ config('app.name', 'Laravel') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto mt-3 mt-md-0 ms-md-4">
                        @foreach ($visibleMenus as $menu)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($menu['active']) ? 'active' : '' }}"
                                   href="{{ route($menu['route']) }}">
                                    {{ $menu['label'] }}
                                </a>
                            </li>
                        @endforeach

                        @if ($visibleReports->isNotEmpty())
                            <li class="nav-item dropdown">
                                <a id="reportDropdown" class="nav-link dropdown-toggle {{ request()->routeIs(...$visibleReports->pluck('active')->flatten()->all()) ? 'active' : '' }}"
                                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Laporan
                                </a>

                                <div class="dropdown-menu" aria-labelledby="reportDropdown">
                                    @foreach ($visibleReports as $report)
                                        <a class="dropdown-item {{ request()->routeIs($report['active']) ? 'active' : '' }}"
                                           href="{{ route($report['route']) }}">
                                            {{ $report['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        @endif
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto mt-2 mt-md-0">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle inv-user" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <span class="inv-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                                    <span>{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="inv-main">
            @yield('content')
        </main>
    </div>
</body>
</html>
