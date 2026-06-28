<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UsuarioSistema;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsuarioSistemaWebController extends Controller
{
    private const ROLES = ['admin', 'tecnico', 'consulta'];

    public function __construct(
        private readonly AuditoriaService $auditoria,
    ) {
    }

    public function index(Request $request): View
    {
        $this->autorizarAdmin();

        $filtros = $request->only(['buscar', 'rol', 'activo']);

        $usuarios = UsuarioSistema::query()
            ->when($filtros['buscar'] ?? null, function ($query, string $buscar): void {
                $query->where(function ($subquery) use ($buscar): void {
                    $subquery->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->when($filtros['rol'] ?? null, fn ($query, string $rol) => $query->where('rol', $rol))
            ->when(($filtros['activo'] ?? '') !== '', fn ($query) => $query->where('activo', (bool) $filtros['activo']))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'filtros' => $filtros,
            'roles' => self::ROLES,
        ]);
    }

    public function create(): View
    {
        $this->autorizarAdmin();

        return view('usuarios.form', [
            'usuarioSistema' => new UsuarioSistema(['activo' => true, 'rol' => 'tecnico']),
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->autorizarAdmin();

        $datos = $this->validar($request);

        $usuario = UsuarioSistema::query()->create([
            'nombre' => $datos['nombre'],
            'email' => $datos['email'],
            'password_hash' => Hash::make($datos['password']),
            'rol' => $datos['rol'],
            'activo' => $request->boolean('activo', true),
        ]);

        $this->auditoria->registrar('insertar', 'usuarios_sistema', $usuario->id, nuevos: [
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->rol,
            'activo' => $usuario->activo,
        ]);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado correctamente.');
    }

    public function edit(UsuarioSistema $usuario): View
    {
        $this->autorizarAdmin();

        return view('usuarios.form', [
            'usuarioSistema' => $usuario,
            'roles' => self::ROLES,
        ]);
    }

    public function update(Request $request, UsuarioSistema $usuario): RedirectResponse
    {
        $this->autorizarAdmin();

        $anteriores = $usuario->only(['nombre', 'email', 'rol', 'activo']);
        $datos = $this->validar($request, $usuario);
        $activo = $request->boolean('activo');

        $this->impedirPerderAdministrador($usuario, $datos['rol'], $activo);

        $usuario->fill([
            'nombre' => $datos['nombre'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
            'activo' => $activo,
        ]);

        if (($datos['password'] ?? null) !== null) {
            $usuario->password_hash = Hash::make($datos['password']);
        }

        $usuario->save();

        $this->auditoria->registrar('actualizar', 'usuarios_sistema', $usuario->id, anteriores: $anteriores, nuevos: [
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->rol,
            'activo' => $usuario->activo,
            'password_actualizado' => ($datos['password'] ?? null) !== null,
        ]);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado.');
    }

    public function toggle(UsuarioSistema $usuario): RedirectResponse
    {
        $this->autorizarAdmin();
        $this->impedirAutogestionRiesgosa($usuario, 'No puedes desactivar tu propia cuenta.');
        $this->impedirPerderAdministrador($usuario, $usuario->rol, ! $usuario->activo);

        $anterior = $usuario->activo;
        $usuario->forceFill(['activo' => ! $usuario->activo])->save();

        $this->auditoria->registrar('actualizar', 'usuarios_sistema', $usuario->id, anteriores: [
            'activo' => $anterior,
        ], nuevos: [
            'activo' => $usuario->activo,
        ]);

        return redirect()->route('usuarios.index')->with('exito', $usuario->activo ? 'Usuario activado.' : 'Usuario desactivado.');
    }

    public function resetPassword(Request $request, UsuarioSistema $usuario): RedirectResponse
    {
        $this->autorizarAdmin();
        $this->impedirAutogestionRiesgosa($usuario, 'Usa el formulario de edicion para cambiar tu propia contrasena.');

        $datos = $request->validate([
            'password' => ['required', 'string', 'max:72', $this->reglaPassword()],
        ]);

        $usuario->forceFill(['password_hash' => Hash::make($datos['password'])])->save();

        $this->auditoria->registrar('actualizar', 'usuarios_sistema', $usuario->id, nuevos: [
            'password_actualizado' => true,
        ]);

        return redirect()->route('usuarios.index')->with('exito', 'Contraseña restablecida.');
    }

    private function validar(Request $request, ?UsuarioSistema $usuario = null): array
    {
        $request->merge([
            'nombre' => trim((string) $request->input('nombre')),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        return $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('usuarios_sistema', 'email')->ignore($usuario?->getKey())],
            'rol' => ['required', Rule::in(self::ROLES)],
            'activo' => ['nullable', 'boolean'],
            'password' => [$usuario?->exists ? 'nullable' : 'required', 'string', 'max:72', $this->reglaPassword()],
        ]);
    }

    private function reglaPassword(): Password
    {
        return Password::min(12)->letters()->mixedCase()->numbers()->symbols();
    }

    private function autorizarAdmin(): void
    {
        abort_unless(auth()->user()?->rol === 'admin', 403, 'Solo administradores pueden gestionar usuarios.');
    }

    private function impedirAutogestionRiesgosa(UsuarioSistema $usuario, string $mensaje): void
    {
        if (auth()->id() === $usuario->id) {
            throw ValidationException::withMessages(['usuario' => $mensaje]);
        }
    }

    private function impedirPerderAdministrador(UsuarioSistema $usuario, string $rol, bool $activo): void
    {
        if (auth()->id() === $usuario->id && ($rol !== 'admin' || ! $activo)) {
            throw ValidationException::withMessages([
                'usuario' => 'No puedes quitar permisos de administrador a tu propia cuenta.',
            ]);
        }

        $dejaDeSerAdminActivo = $usuario->rol === 'admin' && $usuario->activo && ($rol !== 'admin' || ! $activo);

        if (! $dejaDeSerAdminActivo) {
            return;
        }

        $otrosAdmins = UsuarioSistema::query()
            ->whereKeyNot($usuario->id)
            ->where('rol', 'admin')
            ->where('activo', true)
            ->exists();

        if (! $otrosAdmins) {
            throw ValidationException::withMessages([
                'usuario' => 'Debe quedar al menos un administrador activo en el sistema.',
            ]);
        }
    }
}
