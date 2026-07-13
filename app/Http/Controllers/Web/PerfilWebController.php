<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UsuarioSistema;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PerfilWebController extends Controller
{
    public function __construct(private readonly AuditoriaService $auditoria) {}

    public function edit(Request $request): View
    {
        return view('perfil.edit', ['usuario' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var UsuarioSistema $usuario */
        $usuario = $request->user();
        $request->merge([
            'nombre' => trim((string) $request->input('nombre')),
            'email' => Str::lower(trim((string) $request->input('email'))),
            'telefono' => trim((string) $request->input('telefono')) ?: null,
        ]);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('usuarios_sistema', 'email')->ignore($usuario->id)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'eliminar_foto' => ['nullable', 'boolean'],
            'password_actual' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', 'max:72', Password::min(10)->letters()->numbers()],
        ], [
            'foto.max' => 'La imagen no puede pesar más de 2 MB.',
            'password_actual.current_password' => 'La contraseña actual no es correcta.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $anteriores = $usuario->only(['nombre', 'email', 'telefono', 'foto_perfil']);

        if ($request->boolean('eliminar_foto') || $request->hasFile('foto')) {
            if ($usuario->foto_perfil) {
                Storage::disk('local')->delete($usuario->foto_perfil);
            }
            $usuario->foto_perfil = null;
        }

        if ($request->hasFile('foto')) {
            $usuario->foto_perfil = $request->file('foto')->store('perfiles', 'local');
        }

        $usuario->fill($datos);

        if (! empty($datos['password'])) {
            $usuario->password_hash = Hash::make($datos['password']);
        }

        $usuario->save();

        $this->auditoria->registrar('actualizar', 'usuarios_sistema', $usuario->id, anteriores: $anteriores, nuevos: [
            ...$usuario->only(['nombre', 'email', 'telefono', 'foto_perfil']),
            'password_actualizado' => ! empty($datos['password']),
        ]);

        return back()->with('exito', 'Tu perfil se actualizó correctamente.');
    }

    public function foto(Request $request): BinaryFileResponse
    {
        $ruta = $request->user()->foto_perfil;
        abort_unless($ruta && Storage::disk('local')->exists($ruta), 404);

        return response()->file(Storage::disk('local')->path($ruta), [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
