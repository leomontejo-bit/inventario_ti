@extends('layouts.app')
@section('titulo', $contrato->exists ? 'Editar contrato' : 'Nuevo contrato')

@section('contenido')
@php $editando = $contrato->exists; @endphp

<a href="{{ route('contratos.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver
</a>

<form method="POST" action="{{ $editando ? route('contratos.update', $contrato) : route('contratos.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Equipo vinculado *</label>
                <select name="activo_id" required class="input">
                    <option value="">Seleccioná…</option>
                    @foreach ($activos as $a)
                        <option value="{{ $a->id }}" @selected(old('activo_id', $contrato->activo_id) == $a->id)>{{ $a->num_inventario }} — {{ $a->nombre_equipo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Tipo *</label>
                <select name="tipo" required class="input">
                    @foreach (['leasing','mantenimiento','garantia','soporte','otro'] as $t)
                        <option value="{{ $t }}" @selected(old('tipo', $contrato->tipo) == $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Proveedor *</label>
                <input type="text" name="proveedor" value="{{ old('proveedor', $contrato->proveedor) }}" required class="input">
            </div>
            <div>
                <label class="label">N° de contrato</label>
                <input type="text" name="num_contrato" value="{{ old('num_contrato', $contrato->num_contrato) }}" class="input">
            </div>
            <div>
                <label class="label">Contacto proveedor</label>
                <input type="text" name="contacto_proveedor" value="{{ old('contacto_proveedor', $contrato->contacto_proveedor) }}" class="input">
            </div>
            <div>
                <label class="label">Teléfono proveedor</label>
                <input type="text" name="telefono_proveedor" value="{{ old('telefono_proveedor', $contrato->telefono_proveedor) }}" class="input">
            </div>
            <div>
                <label class="label">Fecha de inicio *</label>
                <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $contrato->fecha_inicio?->format('Y-m-d')) }}" required class="input">
            </div>
            <div>
                <label class="label">Fecha de fin</label>
                <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $contrato->fecha_fin?->format('Y-m-d')) }}" class="input">
            </div>
            <div>
                <label class="label">Monto</label>
                <input type="number" step="0.01" name="monto" value="{{ old('monto', $contrato->monto) }}" class="input">
            </div>
            <div>
                <label class="label">Moneda</label>
                <input type="text" name="moneda" maxlength="3" value="{{ old('moneda', $contrato->moneda ?? 'MXN') }}" class="input">
            </div>
            <div>
                <label class="label">Estado</label>
                <select name="estado" class="input">
                    @foreach (['vigente','vencido','cancelado'] as $e)
                        <option value="{{ $e }}" @selected(old('estado', $contrato->estado ?? 'vigente') == $e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('contratos.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear contrato' }}</button>
    </div>
</form>
@endsection
