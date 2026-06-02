<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contrato\StoreContratoRequest;
use App\Http\Requests\Contrato\UpdateContratoRequest;
use App\Models\Activo;
use App\Models\Contrato;
use App\Services\ContratoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContratoWebController extends Controller
{
    public function __construct(
        private readonly ContratoService $contratos,
    ) {
    }

    public function index(Request $request): View
    {
        $filtros = $request->only(['estado', 'tipo', 'buscar']);

        return view('contratos.index', [
            'contratos' => $this->contratos->listar($filtros, 15)->withQueryString(),
            'filtros' => $filtros,
        ]);
    }

    public function create(): View
    {
        return view('contratos.form', $this->datosFormulario(new Contrato()));
    }

    public function store(StoreContratoRequest $request): RedirectResponse
    {
        $this->contratos->crear($request->validated());

        return redirect()->route('contratos.index')->with('exito', 'Contrato registrado.');
    }

    public function edit(Contrato $contrato): View
    {
        return view('contratos.form', $this->datosFormulario($contrato));
    }

    public function update(UpdateContratoRequest $request, Contrato $contrato): RedirectResponse
    {
        $this->contratos->actualizar($contrato, $request->validated());

        return redirect()->route('contratos.index')->with('exito', 'Contrato actualizado.');
    }

    public function destroy(Contrato $contrato): RedirectResponse
    {
        $this->contratos->eliminar($contrato);

        return redirect()->route('contratos.index')->with('exito', 'Contrato eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(Contrato $contrato): array
    {
        return [
            'contrato' => $contrato,
            'activos' => Activo::orderBy('num_inventario')->get(['id', 'num_inventario', 'nombre_equipo']),
        ];
    }
}
