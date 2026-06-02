<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\Colaborador;
use App\Models\Contrato;
use App\Models\LicenciaSoftware;
use App\Models\Views\ContratoPorVencer;
use App\Models\Views\LicenciaPorVencer;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tarjetas = [
            'total_activos' => Activo::count(),
            'en_stock' => Activo::where('estado', 'stock')->count(),
            'asignados' => Activo::where('estado', 'activo')->count(),
            'colaboradores' => Colaborador::count(),
            'licencias' => LicenciaSoftware::count(),
            'contratos' => Contrato::count(),
        ];

        // Distribución por estado para una mini-gráfica de barras
        $porEstado = Activo::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('dashboard', [
            'tarjetas' => $tarjetas,
            'porEstado' => $porEstado,
            'licenciasPorVencer' => LicenciaPorVencer::orderBy('dias_restantes')->limit(5)->get(),
            'contratosPorVencer' => ContratoPorVencer::orderBy('dias_restantes')->limit(5)->get(),
            'ultimosActivos' => Activo::with(['tipoActivo', 'hotel'])->latest('id')->limit(6)->get(),
        ]);
    }
}
