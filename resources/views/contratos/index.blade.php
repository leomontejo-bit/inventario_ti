@extends('layouts.app')
@section('titulo', 'Contratos')

@section('contenido')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Contratos y leasing</h2>
        <p class="text-sm text-gray-500">{{ $contratos->total() }} contratos</p>
    </div>
    <a href="{{ route('contratos.create') }}" class="btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nuevo contrato
    </a>
</div>

<form method="GET" class="card mb-5 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-50 flex-1">
        <label class="label">Buscar</label>
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Proveedor o N° contrato…" class="input">
    </div>
    <div>
        <label class="label">Tipo</label>
        <select name="tipo" class="input">
            <option value="">Todos</option>
            @foreach (['leasing','mantenimiento','garantia','soporte','otro'] as $t)
                <option value="{{ $t }}" @selected(($filtros['tipo'] ?? '') == $t)>{{ ucfirst($t) }}</option>
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
                    <th class="th">Proveedor</th>
                    <th class="th">Tipo</th>
                    <th class="th">N° Contrato</th>
                    <th class="th">Vence</th>
                    <th class="th">Estado</th>
                    <th class="th text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($contratos as $con)
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td font-semibold text-gray-900">{{ $con->proveedor }}</td>
                        <td class="td capitalize">{{ $con->tipo }}</td>
                        <td class="td">{{ $con->num_contrato ?? '—' }}</td>
                        <td class="td">{{ $con->fecha_fin?->format('d/m/Y') ?? '—' }}</td>
                        <td class="td"><span class="badge bg-gray-100 capitalize text-gray-600">{{ $con->estado }}</span></td>
                        <td class="td">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('contratos.edit', $con) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50">Editar</a>
                                <form action="{{ route('contratos.destroy', $con) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="td py-12 text-center text-gray-400">No hay contratos todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $contratos->links() }}</div>
@endsection
