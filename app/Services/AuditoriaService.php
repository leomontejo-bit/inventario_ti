<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditoriaService
{
    /**
     * Registra un evento en la bitácora de auditoría.
     *
     * @param  array<string, mixed>|null  $anteriores
     * @param  array<string, mixed>|null  $nuevos
     */
    public function registrar(
        string $accion,
        string $tabla,
        int $registroId,
        ?int $activoId = null,
        ?int $usuarioSistemaId = null,
        ?array $anteriores = null,
        ?array $nuevos = null,
    ): Auditoria {
        return Auditoria::create([
            'activo_id' => $activoId,
            'usuario_sistema_id' => $usuarioSistemaId ?? Auth::id(),
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'accion' => $accion,
            'valores_anteriores' => $anteriores,
            'valores_nuevos' => $nuevos,
            'ip_cliente' => Request::ip(),
            'fecha' => now(),
        ]);
    }
}
