@extends('layouts.app')
@section('titulo', 'Licencias')

@section('contenido')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Licencias de software</h2>
        <p class="text-sm text-gray-500">{{ $licencias->total() }} licencias</p>
    </div>
    <a href="{{ route('licencias.create') }}" class="btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nueva licencia
    </a>
</div>

<form method="GET" class="card mb-5 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-50 flex-1">
        <label class="label">Buscar</label>
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Software o fabricante…" class="input">
    </div>
    <div>
        <label class="label">Estado</label>
        <select name="estado" class="input">
            <option value="">Todos</option>
            @foreach (['activa','vencida','baja'] as $e)
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
                    <th class="th">Software</th>
                    <th class="th">Tipo</th>
                    <th class="th">Licencias</th>
                    <th class="th">Vencimiento</th>
                    <th class="th">Estado</th>
                    <th class="th text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($licencias as $lic)
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td font-semibold text-gray-900">{{ $lic->nombre_software }} <span class="font-normal text-gray-400">{{ $lic->version }}</span></td>
                        <td class="td capitalize">{{ $lic->tipo_licencia }}</td>
                        <td class="td">{{ $lic->num_licencias }}</td>
                        <td class="td">{{ $lic->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                        <td class="td"><span class="badge bg-gray-100 capitalize text-gray-600">{{ $lic->estado }}</span></td>
                        <td class="td">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('licencias.edit', $lic) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50">Editar</a>
                                <form action="{{ route('licencias.destroy', $lic) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="td py-12 text-center text-gray-400">No hay licencias todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $licencias->links() }}</div>
@endsection
