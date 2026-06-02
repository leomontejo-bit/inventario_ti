@extends('layouts.app')
@section('titulo', 'Activos')

@section('contenido')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Activos TI</h2>
        <p class="text-sm text-gray-500">{{ $activos->total() }} equipos registrados</p>
    </div>
    <a href="{{ route('activos.create') }}" class="btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nuevo activo
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="card mb-5 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-50 flex-1">
        <label class="label">Buscar</label>
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="N° inventario, serie, equipo, IP…" class="input">
    </div>
    <div>
        <label class="label">Hotel</label>
        <select name="hotel_id" class="input">
            <option value="">Todos</option>
            @foreach ($hoteles as $h)
                <option value="{{ $h->id }}" @selected(($filtros['hotel_id'] ?? '') == $h->id)>{{ $h->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Tipo</label>
        <select name="tipo_activo_id" class="input">
            <option value="">Todos</option>
            @foreach ($tipos as $t)
                <option value="{{ $t->id }}" @selected(($filtros['tipo_activo_id'] ?? '') == $t->id)>{{ $t->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Estado</label>
        <select name="estado" class="input">
            <option value="">Todos</option>
            @foreach (['stock','activo','mantenimiento','prestamo','extraviado','baja'] as $e)
                <option value="{{ $e }}" @selected(($filtros['estado'] ?? '') == $e)>{{ ucfirst($e) }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn-ghost">Filtrar</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="th">N° Inventario</th>
                    <th class="th">Tipo</th>
                    <th class="th">Equipo</th>
                    <th class="th">Hotel</th>
                    <th class="th">Asignado a</th>
                    <th class="th">Estado</th>
                    <th class="th text-right">Acciones</th>
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
                @forelse ($activos as $activo)
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td font-semibold text-gray-900">{{ $activo->num_inventario }}</td>
                        <td class="td">{{ $activo->tipoActivo?->nombre }}</td>
                        <td class="td">{{ trim($activo->marca.' '.$activo->modelo) ?: '—' }}</td>
                        <td class="td"><span class="badge bg-gray-100 text-gray-600">{{ $activo->hotel?->codigo }}</span></td>
                        <td class="td">{{ $activo->colaborador?->nombre ?? '—' }}</td>
                        <td class="td">
                            <span class="badge {{ $estados[$activo->estado] ?? 'bg-gray-100' }} capitalize">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $activo->estado }}
                            </span>
                        </td>
                        <td class="td">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('activos.show', $activo) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100">Ver</a>
                                <a href="{{ route('activos.edit', $activo) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50">Editar</a>
                                <form action="{{ route('activos.destroy', $activo) }}" method="POST" onsubmit="return confirm('¿Eliminar este activo?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="td py-12 text-center text-gray-400">No hay activos todavía. Creá el primero.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $activos->links() }}</div>
@endsection
