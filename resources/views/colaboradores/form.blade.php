@extends('layouts.app')
@section('titulo', $colaborador->exists ? 'Editar colaborador' : 'Nuevo colaborador')

@section('contenido')
@php $editando = $colaborador->exists; @endphp

<a href="{{ route('colaboradores.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver
</a>

<form method="POST" action="{{ $editando ? route('colaboradores.update', $colaborador) : route('colaboradores.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Nombre completo *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $colaborador->nombre) }}" required class="input">
            </div>
            <div>
                <label class="label">N° de empleado *</label>
                <input type="text" name="num_empleado" value="{{ old('num_empleado', $colaborador->num_empleado) }}" required class="input">
            </div>
            <div>
                <label class="label">Estado</label>
                <select name="estado" class="input">
                    @foreach (['activo','baja','vacaciones','licencia'] as $e)
                        <option value="{{ $e }}" @selected(old('estado', $colaborador->estado ?? 'activo') == $e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Hotel *</label>
                <select name="hotel_id" required class="input">
                    <option value="">Seleccioná…</option>
                    @foreach ($hoteles as $h)
                        <option value="{{ $h->id }}" @selected(old('hotel_id', $colaborador->hotel_id) == $h->id)>{{ $h->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Departamento *</label>
                <select name="departamento_id" required class="input">
                    <option value="">Seleccioná…</option>
                    @foreach ($departamentos as $d)
                        <option value="{{ $d->id }}" @selected(old('departamento_id', $colaborador->departamento_id) == $d->id)>{{ $d->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Email corporativo</label>
                <input type="email" name="email_corporativo" value="{{ old('email_corporativo', $colaborador->email_corporativo) }}" class="input">
            </div>
            <div>
                <label class="label">Usuario AD</label>
                <input type="text" name="usuario_ad" value="{{ old('usuario_ad', $colaborador->usuario_ad) }}" class="input">
            </div>
            <div class="sm:col-span-2">
                <label class="label">Puesto</label>
                <input type="text" name="puesto" value="{{ old('puesto', $colaborador->puesto) }}" class="input">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('colaboradores.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear colaborador' }}</button>
    </div>
</form>
@endsection
