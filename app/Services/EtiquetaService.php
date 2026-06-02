<?php

namespace App\Services;

use App\Models\Activo;
use App\Models\Etiqueta;
use Picqer\Barcode\BarcodeGeneratorSVG;

class EtiquetaService
{
    /**
     * Valor que se codifica en el código de barras.
     * Usa el código de barras propio del activo o, si no tiene, el N° de inventario.
     */
    public function valorCodigo(Activo $activo): string
    {
        return $activo->codigo_barras ?: $activo->num_inventario;
    }

    /**
     * Genera el SVG del código de barras (Code 128), sin depender de la extensión GD.
     */
    public function codigoBarrasSvg(Activo $activo, int $alto = 50, int $ancho = 2): string
    {
        $generator = new BarcodeGeneratorSVG();

        return $generator->getBarcode(
            $this->valorCodigo($activo),
            $generator::TYPE_CODE_128,
            $ancho,
            $alto,
        );
    }

    /**
     * Arma el snapshot de datos que va impreso en la etiqueta (sección 5.2 del proyecto).
     *
     * @return array<string, string|null>
     */
    public function datosEtiqueta(Activo $activo): array
    {
        return [
            'num_inventario' => $activo->num_inventario,
            'codigo_barras' => $this->valorCodigo($activo),
            'num_serie' => $activo->num_serie,
            'nombre_equipo' => $activo->nombre_equipo,
            'departamento' => $activo->departamento?->nombre,
            'hotel' => $activo->hotel?->nombre,
            'hotel_codigo' => $activo->hotel?->codigo,
            'codigo_interno_ti' => $activo->codigo_interno_ti,
        ];
    }

    /**
     * Registra la generación de la etiqueta en la bitácora (tabla etiquetas).
     */
    public function registrar(Activo $activo, string $tipoImpresion, int $usuarioSistemaId): Etiqueta
    {
        return Etiqueta::create([
            'activo_id' => $activo->id,
            'usuario_sistema_id' => $usuarioSistemaId,
            'tipo_impresion' => $tipoImpresion,
            'fecha_generacion' => now(),
            'datos_etiqueta' => $this->datosEtiqueta($activo),
        ]);
    }
}
