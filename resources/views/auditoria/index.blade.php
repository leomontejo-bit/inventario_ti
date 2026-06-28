@extends('layouts.app')
@section('titulo', 'Auditoría')

@section('contenido')
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-950">Auditoría y trazabilidad</h2>
        <p class="mt-1 text-sm text-gray-500">Eventos recientes, cambios realizados y actividad del sistema.</p>
    </div>
    <div class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-gray-600 ring-1 ring-gray-200">
        {{ $registros->total() }} registros
    </div>
</div>

<form method="GET" class="card mb-6 p-4">
    <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto] lg:items-end">
        <div>
            <label class="label">Buscar</label>
            <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="N° inventario o IP..." class="input">
        </div>
        <div>
            <label class="label">Acción</label>
            <select name="accion" class="input">
                <option value="">Todas</option>
                @foreach ($acciones as $a)
                    <option value="{{ $a }}" @selected(($filtros['accion'] ?? '') == $a)>{{ ucfirst(str_replace('_',' ',$a)) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-primary h-11">Filtrar</button>
    </div>
</form>

@php
    $colorAccion = [
        'insertar' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'actualizar' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'eliminar' => 'bg-red-50 text-red-700 ring-red-200',
        'asignar' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'devolver' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
        'baja' => 'bg-gray-100 text-gray-600 ring-gray-200',
        'importar_excel' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'imprimir_etiqueta' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'escaneo' => 'bg-teal-50 text-teal-700 ring-teal-200',
    ];

    $etiquetas = [
        'num_inventario' => 'N° inventario',
        'codigo_interno_ti' => 'Código TI',
        'codigo_barras' => 'Código de barras',
        'num_serie' => 'N° de serie',
        'nombre_equipo' => 'Nombre del equipo',
        'marca' => 'Marca',
        'modelo' => 'Modelo',
        'procesador' => 'Procesador',
        'ram_gb' => 'RAM (GB)',
        'almacenamiento' => 'Almacenamiento',
        'sistema_operativo' => 'Sistema operativo',
        'direccion_ip' => 'Dirección IP',
        'direccion_mac' => 'Dirección MAC',
        'estado' => 'Estado',
        'fecha_adquisicion' => 'Fecha de adquisición',
        'fecha_baja' => 'Fecha de baja',
        'motivo_baja' => 'Motivo de baja',
        'valor_adquisicion' => 'Valor',
        'observaciones' => 'Observaciones',
        'colaborador_id' => 'Colaborador',
        'tipo_activo_id' => 'Tipo de activo',
        'hotel_id' => 'Hotel',
        'departamento_id' => 'Departamento',
        'password_actualizado' => 'Contraseña actualizada',
        'activo' => 'Activo',
        'rol' => 'Rol',
        'email' => 'Correo',
        'nombre' => 'Nombre',
    ];

    $ocultar = ['id', 'created_at', 'updated_at', 'password_hash'];

    $formatear = function ($valor) {
        if (is_null($valor) || $valor === '') {
            return '—';
        }

        if (is_bool($valor)) {
            return $valor ? 'Sí' : 'No';
        }

        if (is_array($valor)) {
            return json_encode($valor, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $valor;
    };
@endphp

<div class="space-y-3">
    @forelse ($registros as $r)
        @php
            $antes = $r->valores_anteriores ?? [];
            $despues = $r->valores_nuevos ?? [];
            $esActualizacion = $antes && $despues;
            $campos = collect(array_unique(array_merge(array_keys($antes), array_keys($despues))))
                ->reject(fn ($k) => in_array($k, $ocultar, true))
                ->filter(function ($campo) use ($antes, $despues, $esActualizacion, $formatear) {
                    $vAntes = $formatear(array_key_exists($campo, $antes) ? $antes[$campo] : null);
                    $vDespues = $formatear(array_key_exists($campo, $despues) ? $despues[$campo] : null);

                    if ($esActualizacion) {
                        return $vAntes !== $vDespues;
                    }

                    return $vAntes !== '—' || $vDespues !== '—';
                })
                ->values();
            $tieneDetalle = $campos->isNotEmpty();
            $accionTexto = ucfirst(str_replace('_', ' ', $r->accion));
        @endphp

        <article class="card overflow-hidden" x-data="{ abierto: false }">
            <div class="grid gap-4 p-4 lg:grid-cols-[220px_minmax(0,1fr)_170px_140px_auto] lg:items-center">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-900 text-xs font-bold text-white">
                        {{ $r->fecha?->format('H:i') ?? '--:--' }}
                    </span>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-950">{{ $r->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</div>
                        <div class="text-xs text-gray-400">{{ $r->fecha?->format('H:i:s') ?? '' }}</div>
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <span class="badge ring-1 {{ $colorAccion[$r->accion] ?? 'bg-gray-100 text-gray-600 ring-gray-200' }}">{{ $accionTexto }}</span>
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">{{ $r->tabla_afectada }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                        <span class="font-medium text-gray-900">
                            @if ($r->activo)
                                <a href="{{ route('activos.show', $r->activo) }}" class="text-brand-600 hover:text-brand-700">{{ $r->activo->num_inventario }}</a>
                            @else
                                Registro #{{ $r->registro_id }}
                            @endif
                        </span>
                        <span class="text-gray-400">{{ $tieneDetalle ? $campos->count().' campos en detalle' : 'Sin detalle adicional' }}</span>
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Usuario</div>
                    <div class="truncate text-sm font-medium text-gray-700">{{ $r->usuarioSistema?->nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">IP</div>
                    <div class="text-sm font-medium text-gray-700">{{ $r->ip_cliente ?? '—' }}</div>
                </div>

                <div class="flex justify-start lg:justify-end">
                    @if ($tieneDetalle)
                        <button type="button" @click="abierto = !abierto" class="btn-ghost !px-3 !py-2">
                            <span x-text="abierto ? 'Ocultar' : 'Ver'"></span>
                            <svg class="h-4 w-4 transition" :class="abierto && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    @else
                        <span class="text-sm text-gray-300">—</span>
                    @endif
                </div>
            </div>

            @if ($tieneDetalle)
                <div x-show="abierto" x-cloak class="border-t border-gray-100 bg-gray-50/70 p-4">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-gray-900">Detalle del cambio</h3>
                        @if ($esActualizacion)
                            <span class="text-xs font-medium text-amber-700">Solo se muestran campos modificados</span>
                        @endif
                    </div>

                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($campos as $campo)
                            @php
                                $vAntes = $formatear(array_key_exists($campo, $antes) ? $antes[$campo] : null);
                                $vDespues = $formatear(array_key_exists($campo, $despues) ? $despues[$campo] : null);
                                $valor = $vDespues !== '—' ? $vDespues : $vAntes;
                            @endphp

                            <div class="rounded-xl bg-white p-3 ring-1 ring-gray-200">
                                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    {{ $etiquetas[$campo] ?? ucfirst(str_replace('_', ' ', $campo)) }}
                                </div>

                                @if ($esActualizacion)
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <div class="min-w-0 rounded-lg bg-gray-50 px-3 py-2">
                                            <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Antes</div>
                                            <div class="mt-1 break-words text-sm text-gray-500">{{ $vAntes }}</div>
                                        </div>
                                        <div class="min-w-0 rounded-lg bg-emerald-50 px-3 py-2 ring-1 ring-emerald-100">
                                            <div class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Ahora</div>
                                            <div class="mt-1 break-words text-sm font-semibold text-emerald-800">{{ $vDespues }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="break-words text-sm font-semibold text-gray-800">{{ $valor }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>
    @empty
        <div class="card px-6 py-14 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6L19 8.4V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="font-semibold text-gray-700">No hay registros de auditoría todavía.</p>
            <p class="mt-1 text-sm text-gray-400">Cuando el sistema registre cambios aparecerán aquí.</p>
        </div>
    @endforelse
</div>

<div class="mt-5">{{ $registros->links() }}</div>
@endsection
