<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Inventario TI') — Bahia Principe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gray-50 text-gray-800 antialiased">
@php
    $nav = [
        ['dashboard',           'Inicio',        'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10'],
        ['activos.index',       'Activos',       'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['escaneo.index',       'Escaneo',       'M4 7V5a1 1 0 011-1h2M4 17v2a1 1 0 001 1h2m10-16h2a1 1 0 011 1v2m-3 13h2a1 1 0 001-1v-2M7 8v8m4-8v8m3-8v8'],
        ['colaboradores.index', 'Colaboradores', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z'],
        ['licencias.index',     'Licencias',     'M9 12l2 2 4-4m5.6 2A9 9 0 11 12 3a9 9 0 019.6 9z'],
        ['contratos.index',     'Contratos',     'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6L19 8.4V19a2 2 0 01-2 2z'],
        ['catalogos.index',     'Catálogos',     'M4 6h16M4 12h16M4 18h16'],
        ['auditoria.index',     'Auditoría',     'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
    ];

    if (auth()->user()?->rol === 'admin') {
        $nav[] = ['usuarios.index', 'Usuarios', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm8-6a3 3 0 10-6 0 3 3 0 006 0z'];
    }
@endphp

<div x-data="{ open: false }" class="flex min-h-full">
    {{-- ===== Sidebar (mini, se expande al pasar el mouse) ===== --}}
    <aside class="group fixed inset-y-0 left-0 z-40 w-64 -translate-x-full overflow-hidden bg-slate-900 shadow-xl transition-all duration-300 ease-in-out lg:w-20 lg:translate-x-0 lg:hover:w-64"
           :class="open && 'translate-x-0'">
        <div class="flex h-16 items-center gap-2.5 px-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-700 text-white shadow-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div class="leading-tight whitespace-nowrap transition-opacity duration-200 lg:opacity-0 lg:group-hover:opacity-100">
                <div class="text-sm font-bold text-white">Inventario TI</div>
                <div class="text-[11px] text-slate-400">Bahia Principe</div>
            </div>
        </div>

        <nav class="space-y-1 px-3 py-4">
            @foreach ($nav as [$ruta, $texto, $icon])
                <a href="{{ route($ruta) }}" title="{{ $texto }}"
                   class="nav-link {{ request()->routeIs(Str::before($ruta, '.').'*') ? 'nav-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                    </svg>
                    <span class="whitespace-nowrap transition-opacity duration-200 lg:opacity-0 lg:group-hover:opacity-100">{{ $texto }}</span>
                </a>
            @endforeach
        </nav>

        @php
            $usuario = auth()->user();
            $iniciales = $usuario ? Str::of($usuario->nombre)->explode(' ')->take(2)->map(fn ($p) => Str::substr($p, 0, 1))->implode('') : 'TI';
        @endphp
        <div class="absolute inset-x-3 bottom-4 flex items-center gap-3 rounded-xl bg-white/5 p-2.5 ring-1 ring-white/10">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">{{ $iniciales }}</div>
            <div class="min-w-0 flex-1 leading-tight whitespace-nowrap transition-opacity duration-200 lg:opacity-0 lg:group-hover:opacity-100">
                <div class="truncate text-xs font-medium text-white">{{ $usuario?->nombre ?? 'Invitado' }}</div>
                <div class="text-[11px] capitalize text-slate-400">{{ $usuario?->rol ?? '' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="whitespace-nowrap transition-opacity duration-200 lg:opacity-0 lg:group-hover:opacity-100">
                @csrf
                <button type="submit" title="Cerrar sesión" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-white/10 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay móvil --}}
    <div x-show="open" @click="open = false" x-cloak class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    {{-- ===== Contenido ===== --}}
    <div class="flex w-full min-w-0 flex-1 flex-col lg:pl-20">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-gray-200/70 bg-white/80 px-4 backdrop-blur sm:px-6">
            <button @click="open = !open" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="truncate text-lg font-semibold text-gray-900">@yield('titulo', 'Inicio')</h1>
            <div class="ml-auto flex items-center gap-3">
                <span class="hidden whitespace-nowrap text-sm text-gray-400 sm:block">{{ now()->translatedFormat('l, d M Y') }}</span>
            </div>
        </header>

        <main class="min-w-0 flex-1 px-4 py-6 sm:px-6 lg:px-8">
            @if (session('exito'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('exito') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-medium">Revisá estos campos:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('contenido')
        </main>
    </div>
</div>

<style>[x-cloak]{display:none!important}</style>
</body>
</html>
