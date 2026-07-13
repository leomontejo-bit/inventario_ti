@extends('layouts.app')
@section('titulo', 'Mi perfil')

@section('contenido')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Mi perfil</h2>
        <p class="mt-1 text-sm text-gray-500">Actualiza tus datos personales, foto y contraseña.</p>
    </div>

    <form method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card p-6">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                <div class="relative h-24 w-24 shrink-0 overflow-hidden rounded-full bg-gradient-to-br from-brand-500 to-indigo-700 text-white shadow-md">
                    @if ($usuario->foto_perfil)
                        <img src="{{ route('perfil.foto', ['v' => $usuario->updated_at?->timestamp ?? time()]) }}" alt="Foto de perfil" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-2xl font-bold">
                            {{ Str::of($usuario->nombre)->explode(' ')->take(2)->map(fn ($p) => Str::substr($p, 0, 1))->implode('') }}
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <label for="foto" class="label">Foto de perfil</label>
                    <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="input">
                    <p class="mt-2 text-xs text-gray-400">JPG, PNG o WebP. Máximo 2 MB.</p>
                    @if ($usuario->foto_perfil)
                        <label class="mt-3 inline-flex items-center gap-2 text-sm text-red-600">
                            <input type="checkbox" name="eliminar_foto" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Quitar foto actual
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="mb-5 font-semibold text-gray-900">Datos personales</h3>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="nombre" class="label">Nombre completo *</label>
                    <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required maxlength="120" autocomplete="name" class="input">
                </div>
                <div>
                    <label for="email" class="label">Correo electrónico *</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $usuario->email) }}" required maxlength="150" autocomplete="email" class="input">
                </div>
                <div>
                    <label for="telefono" class="label">Teléfono</label>
                    <input id="telefono" type="tel" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" maxlength="30" autocomplete="tel" class="input" placeholder="Ej. +52 998 123 4567">
                </div>
                <div>
                    <span class="label">Rol</span>
                    <div class="input capitalize text-gray-500">{{ $usuario->rol }}</div>
                    <p class="mt-2 text-xs text-gray-400">Solo un administrador puede cambiar el rol.</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="font-semibold text-gray-900">Cambiar contraseña</h3>
            <p class="mb-5 mt-1 text-sm text-gray-500">Déjalo vacío si deseas conservar tu contraseña actual.</p>
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="password_actual" class="label">Contraseña actual</label>
                    <input id="password_actual" type="password" name="password_actual" autocomplete="current-password" class="input">
                </div>
                <div>
                    <label for="password" class="label">Nueva contraseña</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" class="input">
                </div>
                <div>
                    <label for="password_confirmation" class="label">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="input">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn-ghost">Cancelar</a>
            <button class="btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
