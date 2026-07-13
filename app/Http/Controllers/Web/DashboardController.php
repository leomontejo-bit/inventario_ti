<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\Views\ContratoPorVencer;
use App\Models\Views\LicenciaPorVencer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        [$tarjetas, $porEstado] = Cache::remember('dashboard.resumen.v2', now()->addSeconds(20), function (): array {
            // Una sola consulta para las tarjetas; anteriormente se hacían seis.
            $resumen = DB::selectOne(<<<'SQL'
                SELECT
                    (SELECT COUNT(*) FROM activos) AS total_activos,
                    (SELECT COUNT(*) FROM activos WHERE estado = 'stock') AS en_stock,
                    (SELECT COUNT(*) FROM activos WHERE estado = 'activo') AS asignados,
                    (SELECT COUNT(*) FROM colaboradores) AS colaboradores,
                    (SELECT COUNT(*) FROM licencias_software) AS licencias,
                    (SELECT COUNT(*) FROM contratos) AS contratos
                SQL);

            $tarjetas = array_map('intval', (array) $resumen);
            $porEstado = Activo::query()
                ->selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado')
                ->map(fn ($total) => (int) $total)
                ->all();

            return [$tarjetas, $porEstado];
        });

        return view('dashboard', [
            'tarjetas' => $tarjetas,
            'porEstado' => $porEstado,
            'licenciasPorVencer' => LicenciaPorVencer::orderBy('dias_restantes')->limit(5)->get(),
            'contratosPorVencer' => ContratoPorVencer::orderBy('dias_restantes')->limit(5)->get(),
            'ultimosActivos' => Activo::with(['tipoActivo', 'hotel'])->latest('id')->limit(6)->get(),
        ]);
    }
}
