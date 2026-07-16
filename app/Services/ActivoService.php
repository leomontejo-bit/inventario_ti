<?php

namespace App\Services;

use App\Exceptions\EliminacionBloqueadaException;
use App\Models\Activo;
use App\Models\Asignacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ActivoService
{
    public function __construct(
        private readonly AuditoriaService $auditoria,
    ) {}

    /**
     * Listado paginado con filtros opcionales.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listar(array $filtros = [], int $porPagina = 20): LengthAwarePaginator
    {
        return Activo::query()
            ->with(['tipoActivo', 'hotel', 'departamento', 'colaborador'])
            ->when($filtros['hotel_id'] ?? null, fn (Builder $q, $v) => $q->where('hotel_id', $v))
            ->when($filtros['departamento_id'] ?? null, fn (Builder $q, $v) => $q->where('departamento_id', $v))
            ->when($filtros['tipo_activo_id'] ?? null, fn (Builder $q, $v) => $q->where('tipo_activo_id', $v))
            ->when($filtros['estado'] ?? null, fn (Builder $q, $v) => $q->where('estado', $v))
            ->when($filtros['buscar'] ?? null, function (Builder $q, $v) {
                $q->where(function (Builder $sub) use ($v) {
                    $sub->where('num_inventario', 'like', "%{$v}%")
                        ->orWhere('num_serie', 'like', "%{$v}%")
                        ->orWhere('nombre_equipo', 'like', "%{$v}%")
                        ->orWhere('direccion_ip', 'like', "%{$v}%");
                });
            })
            ->latest('id')
            ->paginate($porPagina);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos): Activo
    {
        return DB::transaction(function () use ($datos) {
            $activo = Activo::create($datos);

            $this->auditoria->registrar(
                accion: 'insertar',
                tabla: 'activos',
                registroId: $activo->id,
                activoId: $activo->id,
                nuevos: $activo->getAttributes(),
            );

            return $activo;
        });
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Activo $activo, array $datos): Activo
    {
        return DB::transaction(function () use ($activo, $datos) {
            $anteriores = $activo->getOriginal();
            $activo->update($datos);

            $this->auditoria->registrar(
                accion: 'actualizar',
                tabla: 'activos',
                registroId: $activo->id,
                activoId: $activo->id,
                anteriores: $anteriores,
                nuevos: $activo->getChanges(),
            );

            return $activo;
        });
    }

    public function eliminar(Activo $activo): void
    {
        if ($activo->estado !== 'baja') {
            $dependencias = collect([
                'contrato(s)' => $activo->contratos()->count(),
                'licencia(s)' => $activo->licencias()->count(),
                'asignación(es) en el historial' => $activo->asignaciones()->count(),
                'etiqueta(s) generada(s)' => $activo->etiquetas()->count(),
            ])->filter()->map(fn (int $cantidad, string $nombre) => "{$cantidad} {$nombre}");

            if ($dependencias->isNotEmpty()) {
                throw new EliminacionBloqueadaException(
                    'tiene '.($dependencias->join(', ', ' y ')).' asociado(s).',
                    'conserva su historial y usa “Dar de baja”. Una vez dado de baja podrás eliminarlo definitivamente.',
                );
            }
        }

        DB::transaction(function () use ($activo) {
            $id = $activo->id;
            $anteriores = $activo->getAttributes();

            if ($activo->estado === 'baja') {
                $activo->asignaciones()->delete();
                $activo->contratos()->delete();
                $activo->etiquetas()->delete();
                $activo->licencias()->update(['activo_id' => null]);
            }

            $activo->delete();

            $this->auditoria->registrar(
                accion: 'eliminar',
                tabla: 'activos',
                registroId: $id,
                anteriores: $anteriores,
            );
        });
    }

    /**
     * Asigna un activo a un colaborador y abre el historial de asignación.
     */
    public function asignar(
        Activo $activo,
        int $colaboradorId,
        int $usuarioSistemaId,
        string $fechaAsignacion,
        string $condicionEntrega = 'bueno',
        ?string $notas = null,
    ): Asignacion {
        return DB::transaction(function () use ($activo, $colaboradorId, $usuarioSistemaId, $fechaAsignacion, $condicionEntrega, $notas) {
            $asignacion = Asignacion::create([
                'activo_id' => $activo->id,
                'colaborador_id' => $colaboradorId,
                'usuario_sistema_id' => $usuarioSistemaId,
                'fecha_asignacion' => $fechaAsignacion,
                'condicion_entrega' => $condicionEntrega,
                'notas' => $notas,
            ]);

            $activo->update([
                'colaborador_id' => $colaboradorId,
                'estado' => 'activo',
            ]);

            $this->auditoria->registrar(
                accion: 'asignar',
                tabla: 'activos',
                registroId: $activo->id,
                activoId: $activo->id,
                nuevos: ['colaborador_id' => $colaboradorId],
            );

            return $asignacion;
        });
    }

    /**
     * Registra la devolución de la asignación vigente y devuelve el activo a stock.
     */
    public function devolver(
        Activo $activo,
        string $fechaDevolucion,
        string $condicionRetorno = 'bueno',
        ?string $motivoDevolucion = null,
    ): void {
        DB::transaction(function () use ($activo, $fechaDevolucion, $condicionRetorno, $motivoDevolucion) {
            $asignacion = $activo->asignacionVigente()->first();

            if ($asignacion) {
                $asignacion->update([
                    'fecha_devolucion' => $fechaDevolucion,
                    'condicion_retorno' => $condicionRetorno,
                    'motivo_devolucion' => $motivoDevolucion,
                ]);
            }

            $activo->update([
                'colaborador_id' => null,
                'estado' => 'stock',
            ]);

            $this->auditoria->registrar(
                accion: 'devolver',
                tabla: 'activos',
                registroId: $activo->id,
                activoId: $activo->id,
            );
        });
    }

    /**
     * Da de baja un activo del inventario.
     */
    public function darDeBaja(Activo $activo, string $fechaBaja, string $motivoBaja): Activo
    {
        return DB::transaction(function () use ($activo, $fechaBaja, $motivoBaja) {
            $activo->update([
                'estado' => 'baja',
                'fecha_baja' => $fechaBaja,
                'motivo_baja' => $motivoBaja,
                'colaborador_id' => null,
            ]);

            $this->auditoria->registrar(
                accion: 'baja',
                tabla: 'activos',
                registroId: $activo->id,
                activoId: $activo->id,
                nuevos: ['estado' => 'baja', 'motivo_baja' => $motivoBaja],
            );

            return $activo;
        });
    }
}
