@extends('layouts.app')
@section('titulo', 'Departamentos')

@section('contenido')
<a href="{{ route('catalogos.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Catálogos
</a>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Departamentos</h2>
        <p class="text-sm text-gray-500">{{ $departamentos->total() }} áreas</p>
    </div>
    <a href="{{ route('catalogos.departamentos.create') }}" class="btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nuevo departamento
    </a>
</div>

<form method="GET" class="card mb-5 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-50 flex-1">
        <label class="label">Buscar</label>
        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Nombre…" class="input">
    </div>
    <button class="btn-ghost">Filtrar</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="th">Nombre</th>
                    <th class="th">Colaboradores</th>
                    <th class="th">Equipos</th>
                    <th class="th text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($departamentos as $d)
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td font-semibold text-gray-900">{{ $d->nombre }}</td>
                        <td class="td">{{ $d->colaboradores_count }}</td>
                        <td class="td">{{ $d->activos_count }}</td>
                        <td class="td">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('catalogos.departamentos.edit', $d) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50">Editar</a>
                                <form action="{{ route('catalogos.departamentos.destroy', $d) }}" method="POST" onsubmit="return confirm('¿Eliminar este departamento?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="td py-12 text-center text-gray-400">No hay departamentos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $departamentos->links() }}</div>
@endsection
