@extends('layouts.app')
@section('titulo', 'Escaneo')

@section('contenido')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Escaneo de código de barras</h2>
    <p class="text-sm text-gray-500">Apuntá la pistola lectora al código del equipo. Cada lectura busca el activo automáticamente.</p>
</div>

<div class="grid gap-6 lg:grid-cols-5">
    {{-- Columna principal: input + resultado --}}
    <div class="space-y-6 lg:col-span-3">
        <form method="GET" action="{{ route('escaneo.index') }}" class="card p-6">
            <label class="label">Código escaneado</label>
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7V5a1 1 0 011-1h2M4 17v2a1 1 0 001 1h2m10-16h2a1 1 0 011 1v2m-3 13h2a1 1 0 001-1v-2M7 8v8m4-8v8m3-8v8"/></svg>
                    </span>
                    <input type="text" name="codigo" id="campoEscaneo" autofocus autocomplete="off"
                           placeholder="Escaneá o escribí el código y presioná Enter…"
                           class="input !mt-0 !py-3 !pl-11 text-lg font-medium tracking-wide">
                </div>
                <button class="btn-primary">Buscar</button>
            </div>
            <p class="mt-2 text-xs text-gray-400">Busca por código de barras o número de inventario.</p>
        </form>

        {{-- Resultado de la última lectura --}}
        @if ($resultado)
            @if ($resultado['encontrado'])
                @php $a = $resultado['activo']; @endphp
                <div class="card overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-gray-100 bg-emerald-50/60 px-6 py-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-gray-900">Equipo encontrado</p>
                            <p class="text-xs text-gray-500">Código: {{ $resultado['codigo'] }}</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-lg font-bold text-gray-900">{{ $a->num_inventario }}</p>
                                <p class="text-sm text-gray-500">{{ trim($a->marca.' '.$a->modelo) ?: $a->tipoActivo?->nombre }}</p>
                            </div>
                            @php
                                $estados = [
                                    'stock' => 'bg-emerald-50 text-emerald-700', 'activo' => 'bg-blue-50 text-blue-700',
                                    'mantenimiento' => 'bg-amber-50 text-amber-700', 'prestamo' => 'bg-violet-50 text-violet-700',
                                    'extraviado' => 'bg-red-50 text-red-700', 'baja' => 'bg-gray-100 text-gray-500',
                                ];
                            @endphp
                            <span class="badge {{ $estados[$a->estado] ?? 'bg-gray-100' }} capitalize"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $a->estado }}</span>
                        </div>
                        <dl class="mt-5 grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                            <div><dt class="text-xs uppercase tracking-wide text-gray-400">Tipo</dt><dd class="font-medium">{{ $a->tipoActivo?->nombre }}</dd></div>
                            <div><dt class="text-xs uppercase tracking-wide text-gray-400">Hotel</dt><dd class="font-medium">{{ $a->hotel?->nombre }}</dd></div>
                            <div><dt class="text-xs uppercase tracking-wide text-gray-400">Departamento</dt><dd class="font-medium">{{ $a->departamento?->nombre }}</dd></div>
                            <div><dt class="text-xs uppercase tracking-wide text-gray-400">Asignado a</dt><dd class="font-medium">{{ $a->colaborador?->nombre ?? '— Sin asignar' }}</dd></div>
                            <div><dt class="text-xs uppercase tracking-wide text-gray-400">N° serie</dt><dd class="font-medium">{{ $a->num_serie ?: '—' }}</dd></div>
                            <div><dt class="text-xs uppercase tracking-wide text-gray-400">IP</dt><dd class="font-medium">{{ $a->direccion_ip ?: '—' }}</dd></div>
                        </dl>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <a href="{{ route('activos.show', $a) }}" class="btn-primary">Ver ficha completa</a>
                            <a href="{{ route('etiquetas.imprimir', $a) }}" target="_blank" class="btn-ghost">Generar etiqueta</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-gray-100 bg-red-50/60 px-6 py-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100 text-red-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-gray-900">No se encontró ningún equipo</p>
                            <p class="text-xs text-gray-500">Código: {{ $resultado['codigo'] }}</p>
                        </div>
                    </div>
                    <div class="px-6 py-5 text-sm text-gray-500">
                        Verificá que el activo esté registrado o creá uno nuevo con este código.
                        <a href="{{ route('activos.create') }}" class="ml-1 font-medium text-brand-600 hover:underline">Registrar activo</a>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Historial de escaneo de la sesión --}}
    <div class="lg:col-span-2">
        <div class="card flex h-full flex-col overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <div>
                    <h3 class="font-semibold text-gray-900">Historial de la sesión</h3>
                    <p class="text-xs text-gray-400">{{ $historial->count() }} lecturas</p>
                </div>
                @if ($historial->isNotEmpty())
                    <form method="POST" action="{{ route('escaneo.limpiar') }}">
                        @csrf
                        <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100">Limpiar</button>
                    </form>
                @endif
            </div>
            <div class="flex-1 overflow-y-auto">
                @forelse ($historial as $h)
                    <div class="flex items-center justify-between border-b border-gray-50 px-5 py-3 last:border-0">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-800">{{ $h['num_inventario'] ?? $h['codigo'] }}</p>
                            <p class="text-xs text-gray-400">{{ $h['hora'] }} @if($h['nombre']) · {{ $h['nombre'] }} @endif</p>
                        </div>
                        @if ($h['encontrado'])
                            <span class="badge bg-emerald-50 capitalize text-emerald-700">{{ $h['estado'] }}</span>
                        @else
                            <span class="badge bg-red-50 text-red-700">No hallado</span>
                        @endif
                    </div>
                @empty
                    <div class="flex h-full flex-col items-center justify-center px-5 py-12 text-center">
                        <svg class="mb-2 h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7V5a1 1 0 011-1h2M4 17v2a1 1 0 001 1h2m10-16h2a1 1 0 011 1v2m-3 13h2a1 1 0 001-1v-2M7 8v8m4-8v8m3-8v8"/></svg>
                        <p class="text-sm text-gray-400">Todavía no escaneaste nada.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    // Mantiene el foco en el campo para que la pistola lectora siempre escriba ahí
    (function () {
        const campo = document.getElementById('campoEscaneo');
        if (!campo) return;
        campo.focus();
        campo.select();
        // Si el usuario hace clic en otro lado, recupera el foco al volver
        document.addEventListener('click', function (e) {
            if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('input')) {
                campo.focus();
            }
        });
    })();
</script>
@endsection
