<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\UsuarioSistema;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EscaneoWebController extends Controller
{
    public function __construct(
        private readonly AuditoriaService $auditoria,
    ) {
    }

    /**
     * Pantalla de escaneo. Si llega un código, lo busca y lo agrega al historial de la sesión.
     */
    public function index(Request $request): View
    {
        $codigo = trim((string) $request->query('codigo', ''));
        $resultado = null;

        if ($codigo !== '') {
            $activo = Activo::with(['tipoActivo', 'hotel', 'departamento', 'colaborador'])
                ->where('codigo_barras', $codigo)
                ->orWhere('num_inventario', $codigo)
                ->first();

            $resultado = [
                'codigo' => $codigo,
                'encontrado' => (bool) $activo,
                'activo' => $activo,
            ];

            $this->registrarHistorial($request, $codigo, $activo);

            if ($activo) {
                $this->auditoria->registrar(
                    accion: 'escaneo',
                    tabla: 'activos',
                    registroId: $activo->id,
                    activoId: $activo->id,
                    usuarioSistemaId: $this->usuarioActual(),
                );
            }
        }

        return view('escaneo.index', [
            'resultado' => $resultado,
            'historial' => collect($request->session()->get('escaneos', []))->reverse()->values(),
        ]);
    }

    public function limpiar(Request $request): RedirectResponse
    {
        $request->session()->forget('escaneos');

        return redirect()->route('escaneo.index')->with('exito', 'Historial de escaneo limpiado.');
    }

    private function registrarHistorial(Request $request, string $codigo, ?Activo $activo): void
    {
        $escaneos = $request->session()->get('escaneos', []);

        $escaneos[] = [
            'codigo' => $codigo,
            'encontrado' => (bool) $activo,
            'num_inventario' => $activo?->num_inventario,
            'nombre' => $activo ? trim($activo->marca.' '.$activo->modelo) : null,
            'estado' => $activo?->estado,
            'hora' => now()->format('H:i:s'),
        ];

        // Limita el historial a los últimos 50 escaneos
        $request->session()->put('escaneos', array_slice($escaneos, -50));
    }

    private function usuarioActual(): int
    {
        return auth()->id() ?? UsuarioSistema::query()
            ->orderByRaw("CASE WHEN rol = 'admin' THEN 0 ELSE 1 END")
            ->value('id') ?? 1;
    }
}
