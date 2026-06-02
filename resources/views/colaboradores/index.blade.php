@extends('layouts.app')
@section('titulo', 'Colaboradores')

@section('contenido')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Colaboradores</h2>
        <p class="text-sm text-gray-500">{{ $colaboradores->total() }} empleados</p>
    </div>
    <a href="{{ route('colaboradores.create') }}" class="btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nuevo colaborador
    </a>
</div>

<form method="GET" class="card mb-5 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-50 flex-1">
        <label class="label">Buscar</label>
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Nombre, N° empleado, email…" class="input">
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
    <button class="btn-ghost">Filtrar</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="th">Colaborador</th>
                    <th class="th">N° Empleado</th>
                    <th class="th">Hotel</th>
                    <th class="th">Departamento</th>
                    <th class="th">Estado</th>
                    <th class="th text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($colaboradores as $c)
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                    {{ Str::of($c->nombre)->explode(' ')->take(2)->map(fn($p) => Str::substr($p, 0, 1))->implode('') }}
                                </span>
                                <span class="font-semibold text-gray-900">{{ $c->nombre }}</span>
                            </div>
                        </td>
                        <td class="td">{{ $c->num_empleado }}</td>
                        <td class="td"><span class="badge bg-gray-100 text-gray-600">{{ $c->hotel?->codigo }}</span></td>
                        <td class="td">{{ $c->departamento?->nombre }}</td>
                        <td class="td"><span class="badge bg-gray-100 capitalize text-gray-600">{{ $c->estado }}</span></td>
                        <td class="td">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('colaboradores.edit', $c) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50">Editar</a>
                                <form action="{{ route('colaboradores.destroy', $c) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="td py-12 text-center text-gray-400">No hay colaboradores todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $colaboradores->links() }}</div>
@endsection
