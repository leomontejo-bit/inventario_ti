<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartamentoWebController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->query('buscar');

        return view('catalogos.departamentos.index', [
            'departamentos' => Departamento::withCount(['colaboradores', 'activos'])
                ->when($buscar, fn ($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
                ->orderBy('nombre')
                ->paginate(15)
                ->withQueryString(),
            'buscar' => $buscar,
        ]);
    }

    public function create(): View
    {
        return view('catalogos.departamentos.form', ['departamento' => new Departamento()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Departamento::create($this->validar($request));

        return redirect()->route('catalogos.departamentos.index')->with('exito', 'Departamento creado.');
    }

    public function edit(Departamento $departamento): View
    {
        return view('catalogos.departamentos.form', ['departamento' => $departamento]);
    }

    public function update(Request $request, Departamento $departamento): RedirectResponse
    {
        $departamento->update($this->validar($request));

        return redirect()->route('catalogos.departamentos.index')->with('exito', 'Departamento actualizado.');
    }

    public function destroy(Departamento $departamento): RedirectResponse
    {
        if ($departamento->activos()->exists() || $departamento->colaboradores()->exists()) {
            return redirect()
                ->route('catalogos.departamentos.index')
                ->withErrors('No se puede eliminar el departamento porque tiene activos o colaboradores asociados.');
        }

        $departamento->delete();

        return redirect()->route('catalogos.departamentos.index')->with('exito', 'Departamento eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'activo' => ['boolean'],
        ]);
    }
}
