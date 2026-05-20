<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nikel Fleet — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: true }">
<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-0 overflow-hidden'"
           class="flex-shrink-0 bg-gray-900 text-white flex flex-col transition-all duration-200 ease-in-out">

        {{-- Logo --}}
        <div class="flex items-center gap-3 h-16 px-5 bg-gray-800">
            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center text-gray-900 font-bold text-sm">N</div>
            <div>
                <p class="text-sm font-bold text-white leading-tight">NIKEL FLEET</p>
                <p class="text-xs text-gray-400">PT Sekawan Media</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('dashboard') ? 'bg-yellow-500 text-gray-900' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            {{-- PEMESANAN (ADMIN ONLY) --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ route('bookings.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('bookings.*') ? 'bg-yellow-500 text-gray-900' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Pemesanan
            </a>
            @endif

            {{-- APPROVAL (MANAGER / APPROVER ONLY) --}}
            @if(auth()->user()->isApprover())
            <a href="{{ route('approvals.index') }}"
            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('approvals.*') ? 'bg-yellow-500 text-gray-900' : 'text-gray-300 hover:bg-gray-700' }}">
                
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Persetujuan Perpindahan
                </span>

                @php
                    $pending = auth()->user()->pendingApprovals()->count();
                @endphp

                @if($pending > 0)
                <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $pending }}
                </span>
                @endif
            </a>
            @endif

            {{-- ADMIN ONLY --}}
            @if(auth()->user()->isAdmin())

            <a href="{{ route('vehicles.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('vehicles.*') ? 'bg-yellow-500 text-gray-900' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Manajemen Kendaraan
            </a>

            <a href="{{ route('drivers.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('drivers.*') ? 'bg-yellow-500 text-gray-900' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Manajemen Driver
            </a>

            @endif

            {{-- LAPORAN (ADMIN + MANAGER) --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isApprover())
            <a href="{{ route('reports.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('reports.*') ? 'bg-yellow-500 text-gray-900' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan & Export
            </a>
            @endif

            {{-- LOG AKTIVITAS (ADMIN + MANAGER) --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isApprover())
            <a href="{{ route('logs.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('logs.*') ? 'bg-yellow-500 text-gray-900' : 'text-gray-300 hover:bg-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Log Aktivitas
            </a>
            @endif
        </nav>

        {{-- User + Logout --}}
        <div class="px-4 py-4 border-t border-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left text-xs text-gray-400 hover:text-white transition px-2 py-1">
                    → Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b flex items-center justify-between px-6 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 hidden md:block">🕐 {{ now()->format('Y-m-d H:i') }} (UTC)</span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 bg-gray-50 border rounded-lg px-3 py-1.5 hover:bg-gray-100 transition">
                        <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-right hidden md:block">
                            <p class="text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                        </svg>
                    </button>

                    <div x-show="open"
                        @click.outside="open = false"
                        x-cloak
                        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50 overflow-hidden">

                        <div class="px-4 py-3 border-b bg-gray-50">
                            <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Pilih Simulasi Akun</p>
                            <p class="text-xs text-gray-400 mt-0.5">Gunakan ini untuk menguji hak akses & workflows persetujuan berjenjang tambang</p>
                        </div>

                        @php
                            $allUsers = \App\Models\User::whereIn('role', ['admin', 'manager'])->get();
                        @endphp

                        <div class="py-2">
                            @foreach($allUsers as $u)
                            <form method="POST" action="{{ route('switch.account', $u) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition text-left
                                            {{ auth()->id() === $u->id ? 'bg-yellow-50' : '' }}">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0
                                                {{ auth()->id() === $u->id ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="text-sm font-semibold text-gray-800">{{ $u->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $u->email }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded font-bold uppercase mt-1 inline-block
                                            {{ $u->role === 'admin' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $u->role === 'admin' ? 'ADMIN' : 'APPROVER ' . $loop->iteration }}
                                        </span>
                                    </div>
                                    @if(auth()->id() === $u->id)
                                    <span class="text-yellow-500 text-lg">✓</span>
                                    @endif
                                </button>
                            </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash --}}
        @if(session('success') || $errors->any())
        <div class="px-6 pt-3">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                ✓ {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                @foreach($errors->all() as $e) <p>• {{ $e }}</p> @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>