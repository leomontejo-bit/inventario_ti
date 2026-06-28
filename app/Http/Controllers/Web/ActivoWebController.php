<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Activo\StoreActivoRequest;
use App\Http\Requests\Activo\UpdateActivoRequest;
use App\Models\Activo;
use App\Models\Colaborador;
use App\Models\Departamento;
use App\Models\Hotel;
use App\Models\TipoActivo;
use App\Services\ActivoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivoWebController extends Controller
{
    public function __construct(
        private readonly ActivoService $activos,
    ) {
    }

    public function index(Request $request): View
    {
        $filtros = $request->only(['hotel_id', 'departamento_id', 'tipo_activo_id', 'estado', 'buscar']);

        return view('activos.index', [
            'activos' => $this->activos->listar($filtros, 15)->withQueryString(),
            'filtros' => $filtros,
            'hoteles' => Hotel::orderBy('nombre')->get(),
            'tipos' => TipoActivo::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('activos.form', $this->datosFormulario(new Activo()));
    }

    public function store(StoreActivoRequest $request): RedirectResponse
    {
        $this->activos->crear($request->validated());

        return redirect()->route('activos.index')->with('exito', 'Activo creado correctamente.');
    }

    public function show(Activo $activo): View
    {
        return view('activos.show', [
            'activo' => $activo->load(['tipoActivo', 'hotel', 'departamento', 'colaborador', 'licencias', 'contratos', 'asignaciones.colaborador']),
            'colaboradores' => Colaborador::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    public function asignar(Request $request, Activo $activo): RedirectResponse
    {
        $datos = $request->validate([
            'colaborador_id' => ['required', 'integer', 'exists:colaboradores,id'],
            'fecha_asignacion' => ['required', 'date'],
            'condicion_entrega' => ['required', 'in:bueno,regular,dañado'],
            'notas' => ['nullable', 'string'],
        ]);

        $this->activos->asignar(
            activo: $activo,
            colaboradorId: $datos['colaborador_id'],
            usuarioSistemaId: $this->usuarioActual(),
            fechaAsignacion: $datos['fecha_asignacion'],
            condicionEntrega: $datos['condicion_entrega'],
            notas: $datos['notas'] ?? null,
        );

        return redirect()->route('activos.show', $activo)->with('exito', 'Equipo asignado correctamente.');
    }

    public function devolver(Request $request, Activo $activo): RedirectResponse
    {
        $datos = $request->validate([
            'fecha_devolucion' => ['required', 'date'],
            'condicion_retorno' => ['required', 'in:bueno,regular,dañado'],
            'motivo_devolucion' => ['nullable', 'string', 'max:255'],
        ]);

        $this->activos->devolver(
            activo: $activo,
            fechaDevolucion: $datos['fecha_devolucion'],
            condicionRetorno: $datos['condicion_retorno'],
            motivoDevolucion: $datos['motivo_devolucion'] ?? null,
        );

        return redirect()->route('activos.show', $activo)->with('exito', 'Equipo devuelto a stock.');
    }

    public function darDeBaja(Request $request, Activo $activo): RedirectResponse
    {
        $datos = $request->validate([
            'fecha_baja' => ['required', 'date'],
            'motivo_baja' => ['required', 'string', 'max:255'],
        ]);

        $this->activos->darDeBaja($activo, $datos['fecha_baja'], $datos['motivo_baja']);

        return redirect()->route('activos.show', $activo)->with('exito', 'Equipo dado de baja.');
    }

    /**
     * Usuario del sistema que registra la acción (el que está logueado).
     */
    private function usuarioActual(): int
    {
        return auth()->id() ?? \App\Models\UsuarioSistema::query()
            ->orderByRaw("CASE WHEN rol = 'admin' THEN 0 ELSE 1 END")
            ->value('id') ?? 1;
    }

    public function edit(Activo $activo): View
    {
        return view('activos.form', $this->datosFormulario($activo));
    }

    public function update(UpdateActivoRequest $request, Activo $activo): RedirectResponse
    {
        $this->activos->actualizar($activo, $request->validated());

        return redirect()->route('activos.index')->with('exito', 'Activo actualizado.');
    }

    public function destroy(Activo $activo): RedirectResponse
    {
        $this->activos->eliminar($activo);

        return redirect()->route('activos.index')->with('exito', 'Activo eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(Activo $activo): array
    {
        return [
            'activo' => $activo,
            'hoteles' => Hotel::orderBy('nombre')->get(),
            'departamentos' => Departamento::orderBy('nombre')->get(),
            'tipos' => TipoActivo::orderBy('nombre')->get(),
            'colaboradores' => Colaborador::orderBy('nombre')->get(),
        ];
    }
}
