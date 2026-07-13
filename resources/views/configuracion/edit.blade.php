@extends('layouts.app')
@section('titulo', 'Configuración del sistema')

@section('contenido')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Apariencia del sistema</h2>
        <p class="mt-1 text-sm text-gray-500">Personaliza el logo y los textos que aparecen en la barra lateral.</p>
    </div>

    <form method="POST" action="{{ route('configuracion.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="card p-6">
            <h3 class="mb-5 font-semibold text-gray-900">Logo</h3>
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-700 text-white shadow-lg">
                    @if ($configuracion['logo'])
                        <img src="{{ route('configuracion.logo', ['v' => md5($configuracion['logo'])]) }}" alt="Logo actual" class="h-full w-full bg-white object-contain p-2">
                    @else
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                <div class="flex-1">
                    <label for="logo" class="label">Seleccionar otro logo</label>
                    <input id="logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="input">
                    <p class="mt-2 text-xs text-gray-400">Se recomienda una imagen cuadrada con fondo transparente. Máximo 2 MB.</p>
                    @if ($configuracion['logo'])
                        <label class="mt-3 inline-flex items-center gap-2 text-sm text-red-600">
                            <input type="checkbox" name="eliminar_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Restaurar el logo predeterminado
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="mb-5 font-semibold text-gray-900">Identidad</h3>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="nombre" class="label">Nombre del sistema *</label>
                    <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $configuracion['nombre']) }}" required maxlength="60" class="input">
                </div>
                <div>
                    <label for="subtitulo" class="label">Empresa o subtítulo</label>
                    <input id="subtitulo" type="text" name="subtitulo" value="{{ old('subtitulo', $configuracion['subtitulo']) }}" maxlength="80" class="input">
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn-ghost">Cancelar</a>
            <button class="btn-primary">Guardar apariencia</button>
        </div>
    </form>
</div>
@endsection
