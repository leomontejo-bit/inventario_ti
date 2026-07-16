@extends('layouts.app')
@section('titulo', $hotel->exists ? 'Editar hotel' : 'Nuevo hotel')

@section('contenido')
@php $editando = $hotel->exists; @endphp

<a href="{{ route('catalogos.hoteles.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a hoteles
</a>

<form method="POST" action="{{ $editando ? route('catalogos.hoteles.update', $hotel) : route('catalogos.hoteles.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card max-w-2xl p-6">
        <h3 class="mb-4 text-sm font-semibold text-gray-900">{{ $editando ? 'Editar hotel' : 'Nuevo hotel' }}</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $hotel->nombre) }}" required class="input">
            </div>
            <div>
                <label class="label">Código *</label>
                <input type="text" name="codigo" value="{{ old('codigo', $hotel->codigo) }}" required placeholder="Ej: TUL" class="input">
            </div>
            <div>
                <label class="label">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $hotel->direccion) }}" class="input">
            </div>
            <div class="sm:col-span-2">
                <input type="hidden" name="activo" value="0">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $editando ? $hotel->activo : true)) class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    Hotel activo
                </label>
            </div>
        </div>
    </div>

    <div class="flex max-w-2xl justify-end gap-3">
        <a href="{{ route('catalogos.hoteles.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear hotel' }}</button>
    </div>
</form>
@endsection
