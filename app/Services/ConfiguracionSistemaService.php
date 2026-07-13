<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ConfiguracionSistemaService
{
    private const ARCHIVO = 'configuracion_sistema.json';

    private ?array $configuracion = null;

    public function obtener(): array
    {
        if ($this->configuracion !== null) {
            return $this->configuracion;
        }

        $valores = [];
        if (Storage::disk('local')->exists(self::ARCHIVO)) {
            $valores = json_decode(Storage::disk('local')->get(self::ARCHIVO), true) ?: [];
        }

        return $this->configuracion = array_merge([
            'nombre' => 'Inventario TI',
            'subtitulo' => 'Bahia Principe',
            'logo' => null,
        ], $valores);
    }

    public function guardar(string $nombre, ?string $subtitulo, ?UploadedFile $logo, bool $eliminarLogo): array
    {
        $configuracion = $this->obtener();

        if (($eliminarLogo || $logo) && $configuracion['logo']) {
            Storage::disk('local')->delete($configuracion['logo']);
            $configuracion['logo'] = null;
        }

        if ($logo) {
            $configuracion['logo'] = $logo->store('sistema', 'local');
        }

        $configuracion['nombre'] = $nombre;
        $configuracion['subtitulo'] = $subtitulo;

        Storage::disk('local')->put(
            self::ARCHIVO,
            json_encode($configuracion, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );

        return $this->configuracion = $configuracion;
    }
}
