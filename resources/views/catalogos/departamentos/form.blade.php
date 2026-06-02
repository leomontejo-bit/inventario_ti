@extends('layouts.app')
@section('titulo', $departamento->exists ? 'Editar departamento' : 'Nuevo departamento')

@section('contenido')
@php $editando = $departamento->exists; @endphp

<a href="{{ route('catalogos.departamentos.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a departamentos
</a>

<form method="POST" action="{{ $editando ? route('catalogos.departamentos.update', $departamento) : route('catalogos.departamentos.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card max-w-2xl p-6">
        <h3 class="mb-4 text-sm font-semibold text-gray-900">{{ $editando ? 'Editar departamento' : 'Nuevo departamento' }}</h3>
        <div>
            <label class="label">Nombre *</label>
            <input type="text" name="nombre" value="{{ old('nombre', $departamento->nombre) }}" required class="input">
        </div>
    </div>

    <div class="flex max-w-2xl justify-end gap-3">
        <a href="{{ route('catalogos.departamentos.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear departamento' }}</button>
    </div>
</form>
@endsection
