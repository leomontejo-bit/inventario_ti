<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Licencia\StoreLicenciaRequest;
use App\Http\Requests\Licencia\UpdateLicenciaRequest;
use App\Models\Activo;
use App\Models\LicenciaSoftware;
use App\Services\LicenciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LicenciaWebController extends Controller
{
    public function __construct(
        private readonly LicenciaService $licencias,
    ) {
    }

    public function index(Request $request): View
    {
        $filtros = $request->only(['estado', 'tipo_licencia', 'buscar']);

        return view('licencias.index', [
            'licencias' => $this->licencias->listar($filtros, 15)->withQueryString(),
            'filtros' => $filtros,
        ]);
    }

    public function create(): View
    {
        return view('licencias.form', $this->datosFormulario(new LicenciaSoftware()));
    }

    public function store(StoreLicenciaRequest $request): RedirectResponse
    {
        $this->licencias->crear($request->validated());

        return redirect()->route('licencias.index')->with('exito', 'Licencia registrada.');
    }

    public function edit(LicenciaSoftware $licencia): View
    {
        return view('licencias.form', $this->datosFormulario($licencia));
    }

    public function update(UpdateLicenciaRequest $request, LicenciaSoftware $licencia): RedirectResponse
    {
        $this->licencias->actualizar($licencia, $request->validated());

        return redirect()->route('licencias.index')->with('exito', 'Licencia actualizada.');
    }

    public function destroy(LicenciaSoftware $licencia): RedirectResponse
    {
        $this->licencias->eliminar($licencia);

        return redirect()->route('licencias.index')->with('exito', 'Licencia eliminada.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(LicenciaSoftware $licencia): array
    {
        return [
            'licencia' => $licencia,
            'activos' => Activo::orderBy('num_inventario')->get(['id', 'num_inventario', 'nombre_equipo']),
        ];
    }
}
