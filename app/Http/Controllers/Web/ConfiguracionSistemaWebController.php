<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ConfiguracionSistemaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConfiguracionSistemaWebController extends Controller
{
    public function __construct(private readonly ConfiguracionSistemaService $configuracion) {}

    public function edit(): View
    {
        $this->autorizar();

        return view('configuracion.edit', ['configuracion' => $this->configuracion->obtener()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->autorizar();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:60'],
            'subtitulo' => ['nullable', 'string', 'max:80'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'eliminar_logo' => ['nullable', 'boolean'],
        ], [
            'logo.max' => 'El logo no puede pesar más de 2 MB.',
        ]);

        $this->configuracion->guardar(
            trim($datos['nombre']),
            trim((string) ($datos['subtitulo'] ?? '')) ?: null,
            $request->file('logo'),
            $request->boolean('eliminar_logo'),
        );

        return back()->with('exito', 'La imagen y los datos del sistema se actualizaron correctamente.');
    }

    public function logo(): BinaryFileResponse
    {
        $ruta = $this->configuracion->obtener()['logo'];
        abort_unless($ruta && Storage::disk('local')->exists($ruta), 404);

        return response()->file(Storage::disk('local')->path($ruta), [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    private function autorizar(): void
    {
        abort_unless(auth()->user()?->rol === 'admin', 403, 'Solo administradores pueden cambiar la configuración del sistema.');
    }
}
