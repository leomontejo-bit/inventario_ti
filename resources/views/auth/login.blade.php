<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — {{ $configuracionSistema['nombre'] }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 antialiased">
<div class="flex min-h-full flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-md">
        {{-- Logo --}}
        <div class="mb-8 flex flex-col items-center">
            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-700 text-white shadow-lg">
                @if ($configuracionSistema['logo'])
                    <img src="{{ route('configuracion.logo', ['v' => md5($configuracionSistema['logo'])]) }}" alt="Logo" class="h-full w-full bg-white object-contain p-1.5">
                @else
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                @endif
            </div>
            <h1 class="mt-4 text-xl font-bold text-white">{{ $configuracionSistema['nombre'] }}</h1>
            <p class="text-sm text-slate-400">{{ $configuracionSistema['subtitulo'] }}</p>
        </div>

        <div class="rounded-2xl bg-white p-8 shadow-xl">
            <h2 class="mb-1 text-lg font-semibold text-gray-900">Iniciar sesión</h2>
            <p class="mb-6 text-sm text-gray-500">Ingresá con tu cuenta del sistema</p>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="usuario@bahiaprincipe.com" class="input">
                </div>
                <div>
                    <label class="label">Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••" class="input">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="recordar" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    Mantener sesión iniciada
                </label>
                <button class="btn-primary w-full !py-3">Ingresar</button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">Sistema de control de activos TI · Acceso restringido</p>
    </div>
</div>
</body>
</html>
