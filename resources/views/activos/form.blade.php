@extends('layouts.app')
@section('titulo', $activo->exists ? 'Editar activo' : 'Nuevo activo')

@section('contenido')
@php $editando = $activo->exists; @endphp

<a href="{{ route('activos.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a activos
</a>

<form method="POST" action="{{ $editando ? route('activos.update', $activo) : route('activos.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card p-6">
        <h3 class="mb-4 text-sm font-semibold text-gray-900">Clasificación</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="label">Tipo de activo *</label>
                <select name="tipo_activo_id" required class="input">
                    <option value="">Seleccioná…</option>
                    @foreach ($tipos as $t)
                        <option value="{{ $t->id }}" @selected(old('tipo_activo_id', $activo->tipo_activo_id) == $t->id)>{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Hotel *</label>
                <select name="hotel_id" required class="input">
                    <option value="">Seleccioná…</option>
                    @foreach ($hoteles as $h)
                        <option value="{{ $h->id }}" @selected(old('hotel_id', $activo->hotel_id) == $h->id)>{{ $h->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Departamento *</label>
                <select name="departamento_id" required class="input">
                    <option value="">Seleccioná…</option>
                    @foreach ($departamentos as $d)
                        <option value="{{ $d->id }}" @selected(old('departamento_id', $activo->departamento_id) == $d->id)>{{ $d->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Estado</label>
                <select name="estado" class="input">
                    @foreach (['stock','activo','mantenimiento','prestamo','extraviado','baja'] as $e)
                        <option value="{{ $e }}" @selected(old('estado', $activo->estado ?? 'stock') == $e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="mb-4 text-sm font-semibold text-gray-900">Identificación</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (['num_inventario' => 'N° Inventario *', 'codigo_interno_ti' => 'Código interno TI', 'codigo_barras' => 'Código de barras', 'num_serie' => 'N° de serie'] as $campo => $label)
                <div>
                    <label class="label">{{ $label }}</label>
                    <input type="text" name="{{ $campo }}" value="{{ old($campo, $activo->$campo) }}" class="input">
                </div>
            @endforeach
        </div>
    </div>

    <div class="card p-6">
        <h3 class="mb-4 text-sm font-semibold text-gray-900">Especificaciones</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                'nombre_equipo' => 'Nombre / Hostname', 'marca' => 'Marca', 'modelo' => 'Modelo', 'procesador' => 'Procesador',
                'ram_gb' => 'RAM (GB)', 'almacenamiento' => 'Almacenamiento', 'sistema_operativo' => 'Sistema operativo',
                'direccion_ip' => 'Dirección IP', 'direccion_mac' => 'Dirección MAC',
            ] as $campo => $label)
                <div>
                    <label class="label">{{ $label }}</label>
                    <input type="text" name="{{ $campo }}" value="{{ old($campo, $activo->$campo) }}" class="input">
                </div>
            @endforeach
        </div>
    </div>

    <div class="card p-6">
        <h3 class="mb-4 text-sm font-semibold text-gray-900">Adquisición</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="label">Fecha de adquisición</label>
                <input type="date" name="fecha_adquisicion" value="{{ old('fecha_adquisicion', $activo->fecha_adquisicion?->format('Y-m-d')) }}" class="input">
            </div>
            <div>
                <label class="label">Valor de adquisición</label>
                <input type="number" step="0.01" name="valor_adquisicion" value="{{ old('valor_adquisicion', $activo->valor_adquisicion) }}" class="input">
            </div>
            <div class="sm:col-span-2">
                <label class="label">Observaciones</label>
                <input type="text" name="observaciones" value="{{ old('observaciones', $activo->observaciones) }}" class="input">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('activos.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear activo' }}</button>
    </div>
</form>
@endsection
