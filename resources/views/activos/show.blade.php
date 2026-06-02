@extends('layouts.app')
@section('titulo', 'Activo '.$activo->num_inventario)

@section('contenido')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('activos.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-gray-500 ring-1 ring-gray-200 hover:bg-gray-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $activo->num_inventario }}</h2>
            <p class="text-sm text-gray-500">{{ trim($activo->marca.' '.$activo->modelo) ?: 'Sin descripción' }}</p>
        </div>
        @php
            $estados = [
                'stock' => 'bg-emerald-50 text-emerald-700', 'activo' => 'bg-blue-50 text-blue-700',
                'mantenimiento' => 'bg-amber-50 text-amber-700', 'prestamo' => 'bg-violet-50 text-violet-700',
                'extraviado' => 'bg-red-50 text-red-700', 'baja' => 'bg-gray-100 text-gray-500',
            ];
        @endphp
        <span class="badge {{ $estados[$activo->estado] ?? 'bg-gray-100' }} capitalize">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $activo->estado }}
        </span>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('etiquetas.imprimir', $activo) }}" target="_blank" class="btn-ghost">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Etiqueta
        </a>
        <a href="{{ route('activos.edit', $activo) }}" class="btn-primary">Editar</a>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="card p-6">
            <h3 class="mb-5 font-semibold text-gray-900">Datos del equipo</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3">
                @php
                    $campos = [
                        'Tipo' => $activo->tipoActivo?->nombre, 'Hotel' => $activo->hotel?->nombre,
                        'Departamento' => $activo->departamento?->nombre,
                        'Asignado a' => $activo->colaborador?->nombre ?? '— Sin asignar',
                        'N° de serie' => $activo->num_serie ?: '—', 'Procesador' => $activo->procesador ?: '—',
                        'RAM' => $activo->ram_gb ? $activo->ram_gb.' GB' : '—', 'Almacenamiento' => $activo->almacenamiento ?: '—',
                        'Sistema operativo' => $activo->sistema_operativo ?: '—', 'IP' => $activo->direccion_ip ?: '—',
                        'MAC' => $activo->direccion_mac ?: '—', 'N° inventario' => $activo->num_inventario,
                    ];
                @endphp
                @foreach ($campos as $label => $valor)
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-400">{{ $label }}</dt>
                        <dd class="mt-0.5 font-medium text-gray-800">{{ $valor }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="card p-6">
            <h3 class="mb-4 font-semibold text-gray-900">Historial de asignaciones</h3>
            @forelse ($activo->asignaciones->sortByDesc('fecha_asignacion') as $asig)
                <div class="flex items-center gap-3 border-b border-gray-50 py-3 last:border-0">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                        {{ Str::substr($asig->colaborador?->nombre ?? '?', 0, 1) }}
                    </span>
                    <div class="flex-1 text-sm">
                        <span class="font-medium text-gray-900">{{ $asig->colaborador?->nombre }}</span>
                        <span class="text-gray-500">— {{ $asig->fecha_asignacion?->format('d/m/Y') }}</span>
                    </div>
                    @if ($asig->fecha_devolucion)
                        <span class="badge bg-gray-100 text-gray-500">Devuelto {{ $asig->fecha_devolucion->format('d/m/Y') }}</span>
                    @else
                        <span class="badge bg-blue-50 text-blue-700"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>Vigente</span>
                    @endif
                </div>
            @empty
                <p class="py-4 text-center text-sm text-gray-400">Sin asignaciones registradas.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        {{-- Panel de acciones --}}
        @if ($activo->estado !== 'baja')
            <div class="card p-6" x-data="{ panel: null }">
                <h3 class="mb-4 font-semibold text-gray-900">Acciones</h3>

                <div class="flex flex-wrap gap-2">
                    @if (! $activo->colaborador_id)
                        <button @click="panel = (panel === 'asignar' ? null : 'asignar')" class="btn-primary">Asignar a colaborador</button>
                    @else
                        <button @click="panel = (panel === 'devolver' ? null : 'devolver')" class="btn-primary">Registrar devolución</button>
                    @endif
                    <button @click="panel = (panel === 'baja' ? null : 'baja')" class="btn-ghost text-red-600 ring-red-200 hover:bg-red-50">Dar de baja</button>
                </div>

                {{-- Form: Asignar --}}
                <form x-show="panel === 'asignar'" x-cloak method="POST" action="{{ route('activos.asignar', $activo) }}" class="mt-5 space-y-3 border-t border-gray-100 pt-5">
                    @csrf
                    <div>
                        <label class="label">Colaborador *</label>
                        <select name="colaborador_id" required class="input">
                            <option value="">Seleccioná…</option>
                            @foreach ($colaboradores as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }} — {{ $c->num_empleado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Fecha *</label>
                            <input type="date" name="fecha_asignacion" value="{{ date('Y-m-d') }}" required class="input">
                        </div>
                        <div>
                            <label class="label">Condición</label>
                            <select name="condicion_entrega" class="input">
                                <option value="bueno">Bueno</option>
                                <option value="regular">Regular</option>
                                <option value="dañado">Dañado</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Notas</label>
                        <input type="text" name="notas" class="input" placeholder="Opcional">
                    </div>
                    <button class="btn-primary w-full">Confirmar asignación</button>
                </form>

                {{-- Form: Devolver --}}
                <form x-show="panel === 'devolver'" x-cloak method="POST" action="{{ route('activos.devolver', $activo) }}" class="mt-5 space-y-3 border-t border-gray-100 pt-5">
                    @csrf
                    <p class="text-sm text-gray-500">Devolviendo equipo de <span class="font-medium text-gray-800">{{ $activo->colaborador?->nombre }}</span></p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Fecha *</label>
                            <input type="date" name="fecha_devolucion" value="{{ date('Y-m-d') }}" required class="input">
                        </div>
                        <div>
                            <label class="label">Condición retorno</label>
                            <select name="condicion_retorno" class="input">
                                <option value="bueno">Bueno</option>
                                <option value="regular">Regular</option>
                                <option value="dañado">Dañado</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Motivo</label>
                        <input type="text" name="motivo_devolucion" class="input" placeholder="Opcional">
                    </div>
                    <button class="btn-primary w-full">Confirmar devolución</button>
                </form>

                {{-- Form: Baja --}}
                <form x-show="panel === 'baja'" x-cloak method="POST" action="{{ route('activos.baja', $activo) }}" class="mt-5 space-y-3 border-t border-gray-100 pt-5"
                      onsubmit="return confirm('¿Dar de baja este equipo? Quedará fuera del inventario activo.')">
                    @csrf
                    <div>
                        <label class="label">Fecha de baja *</label>
                        <input type="date" name="fecha_baja" value="{{ date('Y-m-d') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">Motivo *</label>
                        <input type="text" name="motivo_baja" required class="input" placeholder="Ej: equipo obsoleto, daño irreparable…">
                    </div>
                    <button class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Confirmar baja</button>
                </form>
            </div>
        @else
            <div class="card border-red-100 bg-red-50/50 p-6">
                <h3 class="mb-1 font-semibold text-red-700">Equipo dado de baja</h3>
                <p class="text-sm text-red-600">{{ $activo->fecha_baja?->format('d/m/Y') }} — {{ $activo->motivo_baja }}</p>
            </div>
        @endif

        <div class="card p-6">
            <h3 class="mb-3 font-semibold text-gray-900">Licencias <span class="text-gray-400">({{ $activo->licencias->count() }})</span></h3>
            @forelse ($activo->licencias as $lic)
                <div class="border-b border-gray-50 py-2 text-sm text-gray-700 last:border-0">{{ $lic->nombre_software }} {{ $lic->version }}</div>
            @empty
                <p class="text-sm text-gray-400">—</p>
            @endforelse
        </div>
        <div class="card p-6">
            <h3 class="mb-3 font-semibold text-gray-900">Contratos <span class="text-gray-400">({{ $activo->contratos->count() }})</span></h3>
            @forelse ($activo->contratos as $con)
                <div class="border-b border-gray-50 py-2 text-sm text-gray-700 last:border-0">{{ $con->proveedor }} <span class="text-gray-400">({{ $con->tipo }})</span></div>
            @empty
                <p class="text-sm text-gray-400">—</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
