@extends('layouts.app')
@section('titulo', $usuarioSistema->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('contenido')
@php $editando = $usuarioSistema->exists; @endphp

<a href="{{ route('usuarios.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-700">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver
</a>

<form method="POST" action="{{ $editando ? route('usuarios.update', $usuarioSistema) : route('usuarios.store') }}" class="space-y-5">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="card p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Nombre completo *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $usuarioSistema->nombre) }}" required maxlength="120" class="input">
            </div>
            <div>
                <label class="label">Correo electronico *</label>
                <input type="email" name="email" value="{{ old('email', $usuarioSistema->email) }}" required maxlength="150" class="input">
            </div>
            <div>
                <label class="label">Rol *</label>
                <select name="rol" required class="input">
                    @foreach ($roles as $rol)
                        <option value="{{ $rol }}" @selected(old('rol', $usuarioSistema->rol) === $rol)>{{ ucfirst($rol) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="label">{{ $editando ? 'Nueva contrasena' : 'Contrasena temporal *' }}</label>
                <input type="password" name="password" value="{{ old('password') }}" @required(! $editando) minlength="12" maxlength="72" autocomplete="new-password" class="input" placeholder="Minimo 12 caracteres, mayusculas, minusculas, numeros y simbolos">
                <p class="mt-1 text-xs text-gray-400">{{ $editando ? 'Dejala vacia si no queres cambiarla.' : 'Compartila por un canal seguro. El sistema no la volvera a mostrar.' }}</p>
            </div>
            <div class="sm:col-span-2">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $usuarioSistema->activo ?? true)) class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    Usuario activo
                </label>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Solo administradores pueden gestionar usuarios. Las contrasenas se guardan cifradas y no se pueden consultar despues.
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('usuarios.index') }}" class="btn-ghost">Cancelar</a>
        <button class="btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear usuario' }}</button>
    </div>
</form>
@endsection
