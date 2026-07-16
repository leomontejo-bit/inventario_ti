@extends('layouts.app')
@section('titulo', $tipo->exists ? 'Editar tipo' : 'Nuevo tipo')

@section('contenido')
@php $editando = $tipo->exists; @endphp

<a href="{{ route('catalogos.tipos.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a tipos
</a>

<form method="POST" action="{{ $editando ? route('catalogos.tipos.update', $tipo) : route('catalogos.tipos.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card max-w-2xl p-6">
        <h3 class="mb-4 text-sm font-semibold text-gray-900">{{ $editando ? 'Editar tipo de activo' : 'Nuevo tipo de activo' }}</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $tipo->nombre) }}" required class="input">
            </div>
            <div>
                <label class="label">Categoría *</label>
                <select name="categoria" required class="input">
                    @foreach (['equipo_computo','periferico','red','licencia','contrato','otro'] as $c)
                        <option value="{{ $c }}" @selected(old('categoria', $tipo->categoria ?? 'otro') == $c)>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Prefijo de código</label>
                <input type="text" name="prefijo_codigo" value="{{ old('prefijo_codigo', $tipo->prefijo_codigo) }}" placeholder="Ej: PC" class="input">
            </div>
            <div class="sm:col-span-2">
                <input type="hidden" name="activo" value="0">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $editando ? $tipo->activo : true)) class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    Tipo activo
                </label>
            </div>
        </div>
    </div>

    <div class="flex max-w-2xl justify-end gap-3">
        <a href="{{ route('catalogos.tipos.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear tipo' }}</button>
    </div>
</form>
@endsection
