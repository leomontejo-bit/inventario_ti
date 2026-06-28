<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TipoActivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TipoActivoWebController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->query('buscar');

        return view('catalogos.tipos.index', [
            'tipos' => TipoActivo::withCount('activos')
                ->when($buscar, fn ($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
                ->orderBy('nombre')
                ->paginate(15)
                ->withQueryString(),
            'buscar' => $buscar,
        ]);
    }

    public function create(): View
    {
        return view('catalogos.tipos.form', ['tipo' => new TipoActivo()]);
    }

    public function store(Request $request): RedirectResponse
    {
        TipoActivo::create($this->validar($request));

        return redirect()->route('catalogos.tipos.index')->with('exito', 'Tipo de activo creado.');
    }

    public function edit(TipoActivo $tipo): View
    {
        return view('catalogos.tipos.form', ['tipo' => $tipo]);
    }

    public function update(Request $request, TipoActivo $tipo): RedirectResponse
    {
        $tipo->update($this->validar($request));

        return redirect()->route('catalogos.tipos.index')->with('exito', 'Tipo de activo actualizado.');
    }

    public function destroy(TipoActivo $tipo): RedirectResponse
    {
        if ($tipo->activos()->exists()) {
            return redirect()
                ->route('catalogos.tipos.index')
                ->withErrors('No se puede eliminar el tipo porque tiene activos asociados.');
        }

        $tipo->delete();

        return redirect()->route('catalogos.tipos.index')->with('exito', 'Tipo de activo eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'categoria' => ['required', Rule::in(['equipo_computo', 'periferico', 'red', 'licencia', 'contrato', 'otro'])],
            'prefijo_codigo' => ['nullable', 'string', 'max:10'],
            'activo' => ['boolean'],
        ]);
    }
}
