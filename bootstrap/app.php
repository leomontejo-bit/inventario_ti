<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Última barrera ante relaciones no contempladas o esquemas antiguos:
        // una restricción de integridad durante DELETE nunca debe exponer un error 500.
        $exceptions->render(function (QueryException $e, Request $request) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if (! $request->isMethod('delete') || ! in_array((string) $sqlState, ['23000', '23503'], true)) {
                return null;
            }

            $mensaje = 'No se puede eliminar este registro porque otros datos del sistema dependen de él. Alternativa: elimina o reasigna primero los vínculos relacionados; si necesitas conservar el historial, desactiva o da de baja el registro.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $mensaje,
                    'error' => 'eliminacion_bloqueada',
                ], 409);
            }

            return back()->withErrors($mensaje);
        });
    })->create();
