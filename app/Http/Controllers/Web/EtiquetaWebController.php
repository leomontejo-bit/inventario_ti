<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use App\Models\UsuarioSistema;
use App\Services\EtiquetaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EtiquetaWebController extends Controller
{
    public function __construct(
        private readonly EtiquetaService $etiquetas,
    ) {
    }

    /**
     * Vista imprimible de la etiqueta del activo y registro de la generación.
     */
    public function imprimir(Request $request, Activo $activo): View
    {
        $activo->load(['hotel', 'departamento']);

        $tipo = $request->query('tipo', 'termica');
        if (! in_array($tipo, ['termica', 'estandar', 'pdf'], true)) {
            $tipo = 'termica';
        }

        $this->etiquetas->registrar($activo, $tipo, $this->usuarioActual());

        return view('etiquetas.imprimir', [
            'activo' => $activo,
            'tipo' => $tipo,
            'barcodeSvg' => $this->etiquetas->codigoBarrasSvg($activo, alto: $tipo === 'termica' ? 45 : 60),
            'datos' => $this->etiquetas->datosEtiqueta($activo),
        ]);
    }

    private function usuarioActual(): int
    {
        return auth()->id() ?? UsuarioSistema::query()
            ->orderByRaw("FIELD(rol, 'admin') DESC")
            ->value('id') ?? 1;
    }
}
