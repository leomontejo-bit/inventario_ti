<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Services\EliminacionCatalogoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HotelWebController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->query('buscar');

        return view('catalogos.hoteles.index', [
            'hoteles' => Hotel::withCount('activos')
                ->when($buscar, fn ($q, $v) => $q->where(fn ($s) => $s->where('nombre', 'like', "%{$v}%")->orWhere('codigo', 'like', "%{$v}%")))
                ->orderBy('nombre')
                ->paginate(15)
                ->withQueryString(),
            'buscar' => $buscar,
        ]);
    }

    public function create(): View
    {
        return view('catalogos.hoteles.form', ['hotel' => new Hotel]);
    }

    public function store(Request $request): RedirectResponse
    {
        Hotel::create($this->validar($request));

        return redirect()->route('catalogos.hoteles.index')->with('exito', 'Hotel creado.');
    }

    public function edit(Hotel $hotel): View
    {
        return view('catalogos.hoteles.form', ['hotel' => $hotel]);
    }

    public function update(Request $request, Hotel $hotel): RedirectResponse
    {
        $hotel->update($this->validar($request, $hotel->id));

        return redirect()->route('catalogos.hoteles.index')->with('exito', 'Hotel actualizado.');
    }

    public function destroy(Hotel $hotel, EliminacionCatalogoService $eliminacion): RedirectResponse
    {
        $eliminacion->hotel($hotel);

        return redirect()->route('catalogos.hoteles.index')->with('exito', 'Hotel eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'codigo' => ['required', 'string', 'max:20', Rule::unique('hoteles', 'codigo')->ignore($id)],
            'direccion' => ['nullable', 'string', 'max:200'],
            'activo' => ['boolean'],
        ]);
    }
}
