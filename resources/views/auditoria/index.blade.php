@extends('layouts.app')
@section('titulo', 'Auditoría')

@section('contenido')
<div class="mb-5">
    <h2 class="text-xl font-bold text-gray-900">Auditoría y trazabilidad</h2>
    <p class="text-sm text-gray-500">Registro de todos los cambios realizados en el sistema</p>
</div>

<form method="GET" class="card mb-5 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-50 flex-1">
        <label class="label">Buscar</label>
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="N° inventario o IP…" class="input">
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
    <button class="btn-ghost">Filtrar</button>
</form>

@php
    $colorAccion = [
        'insertar' => 'bg-emerald-50 text-emerald-700', 'actualizar' => 'bg-blue-50 text-blue-700',
        'eliminar' => 'bg-red-50 text-red-700', 'asignar' => 'bg-violet-50 text-violet-700',
        'devolver' => 'bg-cyan-50 text-cyan-700', 'baja' => 'bg-gray-100 text-gray-600',
        'importar_excel' => 'bg-amber-50 text-amber-700', 'imprimir_etiqueta' => 'bg-indigo-50 text-indigo-700',
        'escaneo' => 'bg-teal-50 text-teal-700',
    ];

    // Nombres amigables para los campos
    $etiquetas = [
        'num_inventario' => 'N° inventario', 'codigo_interno_ti' => 'Código TI', 'codigo_barras' => 'Código de barras',
        'num_serie' => 'N° de serie', 'nombre_equipo' => 'Nombre del equipo', 'marca' => 'Marca', 'modelo' => 'Modelo',
        'procesador' => 'Procesador', 'ram_gb' => 'RAM (GB)', 'almacenamiento' => 'Almacenamiento',
        'sistema_operativo' => 'Sistema operativo', 'direccion_ip' => 'Dirección IP', 'direccion_mac' => 'Dirección MAC',
        'estado' => 'Estado', 'fecha_adquisicion' => 'Fecha de adquisición', 'fecha_baja' => 'Fecha de baja',
        'motivo_baja' => 'Motivo de baja', 'valor_adquisicion' => 'Valor', 'observaciones' => 'Observaciones',
        'colaborador_id' => 'Colaborador (ID)', 'tipo_activo_id' => 'Tipo de activo (ID)',
        'hotel_id' => 'Hotel (ID)', 'departamento_id' => 'Departamento (ID)',
    ];
    // Campos técnicos que no aportan al usuario
    $ocultar = ['id', 'created_at', 'updated_at'];

    $formatear = function ($valor) {
        if (is_null($valor) || $valor === '') return '—';
        if (is_bool($valor)) return $valor ? 'Sí' : 'No';
        return (string) $valor;
    };
@endphp

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="th">Fecha y hora</th>
                    <th class="th">Acción</th>
                    <th class="th">Tabla</th>
                    <th class="th">Activo</th>
                    <th class="th">Usuario</th>
                    <th class="th">IP</th>
                    <th class="th text-right">Detalle</th>
                </tr>
            </thead>
            @forelse ($registros as $r)
                <tbody class="divide-y divide-gray-50 border-t border-gray-50" x-data="{ abierto: false }">
                    <tr class="transition hover:bg-gray-50/60">
                        <td class="td whitespace-nowrap">{{ $r->fecha?->format('d/m/Y H:i:s') }}</td>
                        <td class="td"><span class="badge {{ $colorAccion[$r->accion] ?? 'bg-gray-100 text-gray-600' }} capitalize">{{ str_replace('_',' ',$r->accion) }}</span></td>
                        <td class="td text-gray-500">{{ $r->tabla_afectada }}</td>
                        <td class="td font-medium">
                            @if ($r->activo)
                                <a href="{{ route('activos.show', $r->activo) }}" class="text-brand-600 hover:underline">{{ $r->activo->num_inventario }}</a>
                            @else
                                <span class="text-gray-400">#{{ $r->registro_id }}</span>
                            @endif
                        </td>
                        <td class="td">{{ $r->usuarioSistema?->nombre ?? '—' }}</td>
                        <td class="td text-gray-400">{{ $r->ip_cliente ?? '—' }}</td>
                        <td class="td text-right">
                            @if ($r->valores_anteriores || $r->valores_nuevos)
                                <button @click="abierto = !abierto" class="rounded-lg px-2.5 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100" x-text="abierto ? 'Ocultar' : 'Ver'"></button>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @if ($r->valores_anteriores || $r->valores_nuevos)
                        @php
                            $antes = $r->valores_anteriores ?? [];
                            $despues = $r->valores_nuevos ?? [];
                            $esActualizacion = $antes && $despues;
                            // Campos a mostrar: unión de ambos, sin los técnicos
                            $campos = collect(array_keys($antes + $despues))->reject(fn ($k) => in_array($k, $ocultar))->values();
                        @endphp
                        <tr x-show="abierto" x-cloak>
                            <td colspan="7" class="bg-gray-50/70 px-5 py-4">
                                <div class="overflow-hidden rounded-xl bg-white ring-1 ring-gray-200">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-400">
                                            <tr>
                                                <th class="px-4 py-2 text-left font-semibold">Campo</th>
                                                @if ($esActualizacion)
                                                    <th class="px-4 py-2 text-left font-semibold">Antes</th>
                                                    <th class="px-4 py-2 text-left font-semibold">Después</th>
                                                @else
                                                    <th class="px-4 py-2 text-left font-semibold">Valor</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach ($campos as $campo)
                                                @php
                                                    $vAntes = $formatear($antes[$campo] ?? null);
                                                    $vDespues = $formatear($despues[$campo] ?? null);
                                                    $cambio = $esActualizacion && $vAntes !== $vDespues;
                                                @endphp
                                                <tr class="{{ $cambio ? 'bg-amber-50/50' : '' }}">
                                                    <td class="px-4 py-2 font-medium text-gray-600">{{ $etiquetas[$campo] ?? ucfirst(str_replace('_', ' ', $campo)) }}</td>
                                                    @if ($esActualizacion)
                                                        <td class="px-4 py-2 text-gray-400 line-through">{{ $vAntes }}</td>
                                                        <td class="px-4 py-2 font-medium text-gray-800">{{ $vDespues }}</td>
                                                    @else
                                                        <td class="px-4 py-2 text-gray-800">{{ $vDespues !== '—' ? $vDespues : $vAntes }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            @empty
                <tbody>
                    <tr><td colspan="7" class="td py-12 text-center text-gray-400">No hay registros de auditoría todavía.</td></tr>
                </tbody>
            @endforelse
        </table>
    </div>
</div>

<div class="mt-5">{{ $registros->links() }}</div>
@endsection
