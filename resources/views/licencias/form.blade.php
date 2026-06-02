@extends('layouts.app')
@section('titulo', $licencia->exists ? 'Editar licencia' : 'Nueva licencia')

@section('contenido')
@php $editando = $licencia->exists; @endphp

<a href="{{ route('licencias.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver
</a>

<form method="POST" action="{{ $editando ? route('licencias.update', $licencia) : route('licencias.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Nombre del software *</label>
                <input type="text" name="nombre_software" value="{{ old('nombre_software', $licencia->nombre_software) }}" required class="input">
            </div>
            <div>
                <label class="label">Versión</label>
                <input type="text" name="version" value="{{ old('version', $licencia->version) }}" class="input">
            </div>
            <div>
                <label class="label">Fabricante</label>
                <input type="text" name="fabricante" value="{{ old('fabricante', $licencia->fabricante) }}" class="input">
            </div>
            <div>
                <label class="label">Tipo de licencia</label>
                <select name="tipo_licencia" class="input">
                    @foreach (['oem','volumen','suscripcion','freeware','otro'] as $t)
                        <option value="{{ $t }}" @selected(old('tipo_licencia', $licencia->tipo_licencia ?? 'otro') == $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">N° de licencias</label>
                <input type="number" name="num_licencias" min="1" value="{{ old('num_licencias', $licencia->num_licencias ?? 1) }}" class="input">
            </div>
            <div>
                <label class="label">Equipo vinculado</label>
                <select name="activo_id" class="input">
                    <option value="">— Flotante / sin equipo —</option>
                    @foreach ($activos as $a)
                        <option value="{{ $a->id }}" @selected(old('activo_id', $licencia->activo_id) == $a->id)>{{ $a->num_inventario }} — {{ $a->nombre_equipo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Estado</label>
                <select name="estado" class="input">
                    @foreach (['activa','vencida','baja'] as $e)
                        <option value="{{ $e }}" @selected(old('estado', $licencia->estado ?? 'activa') == $e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Fecha de adquisición</label>
                <input type="date" name="fecha_adquisicion" value="{{ old('fecha_adquisicion', $licencia->fecha_adquisicion?->format('Y-m-d')) }}" class="input">
            </div>
            <div>
                <label class="label">Fecha de vencimiento</label>
                <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $licencia->fecha_vencimiento?->format('Y-m-d')) }}" class="input">
            </div>
            <div>
                <label class="label">Proveedor</label>
                <input type="text" name="proveedor" value="{{ old('proveedor', $licencia->proveedor) }}" class="input">
            </div>
            <div>
                <label class="label">Costo</label>
                <input type="number" step="0.01" name="costo" value="{{ old('costo', $licencia->costo) }}" class="input">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('licencias.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear licencia' }}</button>
    </div>
</form>
@endsection
