<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Colaborador\StoreColaboradorRequest;
use App\Http\Requests\Colaborador\UpdateColaboradorRequest;
use App\Models\Colaborador;
use App\Models\Departamento;
use App\Models\Hotel;
use App\Services\ColaboradorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColaboradorWebController extends Controller
{
    public function __construct(
        private readonly ColaboradorService $colaboradores,
    ) {
    }

    public function index(Request $request): View
    {
        $filtros = $request->only(['hotel_id', 'departamento_id', 'estado', 'buscar']);

        return view('colaboradores.index', [
            'colaboradores' => $this->colaboradores->listar($filtros, 15)->withQueryString(),
            'filtros' => $filtros,
            'hoteles' => Hotel::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('colaboradores.form', $this->datosFormulario(new Colaborador()));
    }

    public function store(StoreColaboradorRequest $request): RedirectResponse
    {
        $this->colaboradores->crear($request->validated());

        return redirect()->route('colaboradores.index')->with('exito', 'Colaborador creado.');
    }

    public function edit(Colaborador $colaborador): View
    {
        return view('colaboradores.form', $this->datosFormulario($colaborador));
    }

    public function update(UpdateColaboradorRequest $request, Colaborador $colaborador): RedirectResponse
    {
        $this->colaboradores->actualizar($colaborador, $request->validated());

        return redirect()->route('colaboradores.index')->with('exito', 'Colaborador actualizado.');
    }

    public function destroy(Colaborador $colaborador): RedirectResponse
    {
        $this->colaboradores->eliminar($colaborador);

        return redirect()->route('colaboradores.index')->with('exito', 'Colaborador eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(Colaborador $colaborador): array
    {
        return [
            'colaborador' => $colaborador,
            'hoteles' => Hotel::orderBy('nombre')->get(),
            'departamentos' => Departamento::orderBy('nombre')->get(),
        ];
    }
}
