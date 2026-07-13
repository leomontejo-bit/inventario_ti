@extends('layouts.app')
@section('titulo', 'Inicio')

@section('contenido')
{{-- Encabezado con accesos rápidos --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Resumen general</h2>
        <p class="text-sm text-gray-500">Estado actual del inventario tecnológico</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('activos.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo activo
        </a>
        <a href="{{ route('activos.index') }}" class="btn-ghost">Ver inventario</a>
    </div>
</div>

{{-- KPIs --}}
@php
    $total = max($tarjetas['total_activos'], 1);
    $pctAsignados = round($tarjetas['asignados'] / $total * 100);
    $pctStock = round($tarjetas['en_stock'] / $total * 100);
    $cards = [
        ['Activos totales', $tarjetas['total_activos'], 'Equipos en el sistema', 'from-slate-700 to-slate-900', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10'],
        ['En stock', $tarjetas['en_stock'], $pctStock.'% disponible', 'from-emerald-500 to-emerald-700', 'M5 8h14M5 8a2 2 0 100-4h14a2 2 0 100 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8'],
        ['Asignados', $tarjetas['asignados'], $pctAsignados.'% en uso', 'from-blue-500 to-blue-700', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ['Colaboradores', $tarjetas['colaboradores'], 'Personal registrado', 'from-violet-500 to-violet-700', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z'],
        ['Licencias', $tarjetas['licencias'], 'Software registrado', 'from-amber-500 to-orange-600', 'M9 12l2 2 4-4m5.6 2A9 9 0 1112 3a9 9 0 019.6 9z'],
        ['Contratos', $tarjetas['contratos'], 'Leasing y soporte', 'from-rose-500 to-rose-700', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6L19 8.4V19a2 2 0 01-2 2z'],
    ];
@endphp
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-6">
    @foreach ($cards as [$label, $valor, $sub, $grad, $icon])
        <div class="card p-5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br {{ $grad }} text-white shadow-md">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
            </div>
            <div class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $valor }}</div>
            <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
            <div class="mt-0.5 text-xs text-gray-400">{{ $sub }}</div>
        </div>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
    {{-- Distribución por estado --}}
    <div class="card p-6">
        <h3 class="mb-5 font-semibold text-gray-900">Activos por estado</h3>
        @php
            $maxEstado = max($porEstado ?: [1]);
            $colorBar = [
                'stock' => 'bg-emerald-500', 'activo' => 'bg-blue-500', 'mantenimiento' => 'bg-amber-500',
                'prestamo' => 'bg-violet-500', 'extraviado' => 'bg-red-500', 'baja' => 'bg-gray-400',
            ];
        @endphp
        @forelse ($porEstado as $estado => $cantidad)
            <div class="mb-4">
                <div class="mb-1.5 flex items-center justify-between text-sm">
                    <span class="capitalize text-gray-600">{{ $estado }}</span>
                    <span class="font-semibold text-gray-900">{{ $cantidad }}<span class="ml-1 text-xs font-normal text-gray-400">({{ round($cantidad / $total * 100) }}%)</span></span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-2 rounded-full {{ $colorBar[$estado] ?? 'bg-slate-500' }}" style="width: {{ round($cantidad / $maxEstado * 100) }}%"></div>
                </div>
            </div>
        @empty
            <p class="py-6 text-center text-sm text-gray-400">Sin activos cargados todavía.</p>
        @endforelse
    </div>

    {{-- Licencias por vencer --}}
    <div class="card flex flex-col p-6">
        <div class="mb-4 flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <h3 class="font-semibold text-gray-900">Licencias por vencer</h3>
        </div>
        @forelse ($licenciasPorVencer as $lic)
            <div class="flex items-center justify-between border-b border-gray-50 py-2.5 text-sm last:border-0">
                <span class="text-gray-700">{{ $lic->nombre_software }}</span>
                <span class="badge {{ $lic->dias_restantes < 15 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">{{ $lic->dias_restantes }} días</span>
            </div>
        @empty
            <div class="flex flex-1 flex-col items-center justify-center py-8 text-center">
                <svg class="mb-2 h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-gray-400">Sin vencimientos próximos</p>
            </div>
        @endforelse
    </div>

    {{-- Contratos por vencer --}}
    <div class="card flex flex-col p-6">
        <div class="mb-4 flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6L19 8.4V19a2 2 0 01-2 2z"/></svg>
            </span>
            <h3 class="font-semibold text-gray-900">Contratos por vencer</h3>
        </div>
        @forelse ($contratosPorVencer as $con)
            <div class="flex items-center justify-between border-b border-gray-50 py-2.5 text-sm last:border-0">
                <span class="text-gray-700">{{ $con->proveedor }} <span class="text-gray-400">({{ $con->tipo }})</span></span>
                <span class="badge {{ $con->dias_restantes < 15 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">{{ $con->dias_restantes }} días</span>
            </div>
        @empty
            <div class="flex flex-1 flex-col items-center justify-center py-8 text-center">
                <svg class="mb-2 h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-gray-400">Sin vencimientos próximos</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Últimos activos --}}
<div class="card mt-6 overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
        <h3 class="font-semibold text-gray-900">Últimos activos registrados</h3>
        <a href="{{ route('activos.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Ver todos</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="th">N° Inventario</th>
                    <th class="th">Tipo</th>
                    <th class="th">Equipo</th>
                    <th class="th">Hotel</th>
                    <th class="th">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                    $estados = [
                        'stock' => 'bg-emerald-50 text-emerald-700', 'activo' => 'bg-blue-50 text-blue-700',
                        'mantenimiento' => 'bg-amber-50 text-amber-700', 'prestamo' => 'bg-violet-50 text-violet-700',
                        'extraviado' => 'bg-red-50 text-red-700', 'baja' => 'bg-gray-100 text-gray-500',
                    ];
                @endphp
                @forelse ($ultimosActivos as $a)
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td font-semibold text-gray-900"><a href="{{ route('activos.show', $a) }}" class="hover:text-brand-600">{{ $a->num_inventario }}</a></td>
                        <td class="td">{{ $a->tipoActivo?->nombre }}</td>
                        <td class="td">{{ trim($a->marca.' '.$a->modelo) ?: '—' }}</td>
                        <td class="td"><span class="badge bg-gray-100 text-gray-600">{{ $a->hotel?->codigo }}</span></td>
                        <td class="td"><span class="badge {{ $estados[$a->estado] ?? 'bg-gray-100' }} capitalize"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $a->estado }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="td py-10 text-center text-gray-400">Todavía no hay activos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
