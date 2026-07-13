<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class EliminacionBloqueadaException extends RuntimeException
{
    public function __construct(
        string $motivo,
        public readonly string $alternativa,
    ) {
        parent::__construct("No se puede eliminar este registro porque {$motivo} Alternativa: {$alternativa}");
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => $this->getMessage(),
                'error' => 'eliminacion_bloqueada',
                'alternativa' => $this->alternativa,
            ], 409);
        }

        return back()->withErrors($this->getMessage());
    }
}
