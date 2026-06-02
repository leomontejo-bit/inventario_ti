@extends('layouts.app')
@section('titulo', 'Catálogos')

@section('contenido')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Catálogos</h2>
    <p class="text-sm text-gray-500">Datos maestros del sistema. Elegí qué administrar.</p>
</div>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @php
        $secciones = [
            ['catalogos.hoteles.index', 'Hoteles', 'Propiedades del grupo', $totalHoteles, 'from-blue-500 to-blue-600', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m4-12h2m-2 4h2m4-4h2m-2 4h2'],
            ['catalogos.departamentos.index', 'Departamentos', 'Áreas operativas', $totalDepartamentos, 'from-violet-500 to-violet-600', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['catalogos.tipos.index', 'Tipos de activo', 'Categorías de equipos', $totalTipos, 'from-amber-500 to-orange-500', 'M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z'],
        ];
    @endphp
    @foreach ($secciones as [$ruta, $titulo, $desc, $total, $grad, $icon])
        <a href="{{ route($ruta) }}" class="card group flex items-center gap-4 p-6 transition hover:-translate-y-0.5 hover:shadow-md">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $grad }} text-white shadow-sm">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-gray-900">{{ $titulo }}</h3>
                <p class="text-sm text-gray-400">{{ $desc }}</p>
                <p class="mt-1 text-xs font-medium text-gray-500">{{ $total }} registrados</p>
            </div>
            <svg class="h-5 w-5 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @endforeach
</div>
@endsection
