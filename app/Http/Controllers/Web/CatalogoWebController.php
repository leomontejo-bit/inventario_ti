<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Hotel;
use App\Models\TipoActivo;
use Illuminate\View\View;

class CatalogoWebController extends Controller
{
    /**
     * Hub de catálogos: tarjetas de acceso a cada sección.
     */
    public function index(): View
    {
        return view('catalogos.index', [
            'totalHoteles' => Hotel::count(),
            'totalDepartamentos' => Departamento::count(),
            'totalTipos' => TipoActivo::count(),
        ]);
    }
}
