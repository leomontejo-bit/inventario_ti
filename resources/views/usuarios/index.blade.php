@extends('layouts.app')
@section('titulo', 'Usuarios')

@section('contenido')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Usuarios del sistema</h2>
        <p class="text-sm text-gray-500">{{ $usuarios->total() }} cuentas registradas</p>
    </div>
    <a href="{{ route('usuarios.create') }}" class="btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nuevo usuario
    </a>
</div>

<form method="GET" class="card mb-5 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-56 flex-1">
        <label class="label">Buscar</label>
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Nombre o correo" class="input">
    </div>
    <div>
        <label class="label">Rol</label>
        <select name="rol" class="input">
            <option value="">Todos</option>
            @foreach ($roles as $rol)
                <option value="{{ $rol }}" @selected(($filtros['rol'] ?? '') === $rol)>{{ ucfirst($rol) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Estado</label>
        <select name="activo" class="input">
            <option value="">Todos</option>
            <option value="1" @selected(($filtros['activo'] ?? '') === '1')>Activos</option>
            <option value="0" @selected(($filtros['activo'] ?? '') === '0')>Inactivos</option>
        </select>
    </div>
    <button class="btn-ghost">Filtrar</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="th">Usuario</th>
                    <th class="th">Rol</th>
                    <th class="th">Estado</th>
                    <th class="th">Ultimo acceso</th>
                    <th class="th text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($usuarios as $u)
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold uppercase text-brand-700">
                                    {{ Str::of($u->nombre)->explode(' ')->take(2)->map(fn($p) => Str::substr($p, 0, 1))->implode('') }}
                                </span>
                                <span>
                                    <span class="block font-semibold text-gray-900">{{ $u->nombre }}</span>
                                    <span class="text-xs text-gray-400">{{ $u->email }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="td"><span class="badge bg-gray-100 capitalize text-gray-600">{{ $u->rol }}</span></td>
                        <td class="td">
                            @if ($u->activo)
                                <span class="badge bg-emerald-50 text-emerald-700">Activo</span>
                            @else
                                <span class="badge bg-red-50 text-red-700">Inactivo</span>
                            @endif
                        </td>
                        <td class="td">{{ $u->ultimo_acceso?->format('d/m/Y H:i') ?? 'Sin acceso' }}</td>
                        <td class="td">
                            <div class="flex flex-wrap items-center justify-end gap-1">
                                <a href="{{ route('usuarios.edit', $u) }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-50">Editar</a>
                                @if (auth()->id() !== $u->id)
                                    <form action="{{ route('usuarios.toggle', $u) }}" method="POST" onsubmit="return confirm('Confirmar cambio de estado para este usuario?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium {{ $u->activo ? 'text-amber-700 hover:bg-amber-50' : 'text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $u->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('usuarios.reset-password', $u) }}" method="POST" class="flex items-center gap-1" onsubmit="return confirm('Restablecer contrasena de este usuario?')">
                                        @csrf
                                        <input type="password" name="password" required minlength="12" maxlength="72" autocomplete="new-password" placeholder="Nueva contrasena" class="input !mt-0 !w-44 !py-1.5">
                                        <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Reset</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Tu cuenta</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="td py-12 text-center text-gray-400">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $usuarios->links() }}</div>
@endsection
