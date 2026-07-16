<?php

namespace Tests\Feature;

use App\Models\Activo;
use App\Models\Asignacion;
use App\Models\Auditoria;
use App\Models\Colaborador;
use App\Models\Contrato;
use App\Models\Etiqueta;
use App\Models\Hotel;
use App\Models\UsuarioSistema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioWebTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        // Todas las rutas están protegidas: iniciamos sesión como administrador.
        $admin = UsuarioSistema::query()
            ->orderByRaw("CASE WHEN rol = 'admin' THEN 0 ELSE 1 END")
            ->first();
        if ($admin) {
            $this->actingAs($admin);
        }
    }

    public function test_login_requerido_sin_sesion(): void
    {
        auth()->logout();
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_dashboard_carga(): void
    {
        $this->get('/')->assertOk()->assertSee('Inventario TI');
    }

    public function test_listados_cargan(): void
    {
        foreach (['/activos', '/colaboradores', '/licencias', '/contratos', '/catalogos'] as $ruta) {
            $this->get($ruta)->assertOk();
        }
    }

    public function test_crear_colaborador_desde_formulario(): void
    {
        $response = $this->post('/colaboradores', [
            'nombre' => 'Maria Lopez (WEB-TEST)',
            'num_empleado' => 'WEB-TEST-001',
            'hotel_id' => 1,
            'departamento_id' => 1,
            'estado' => 'activo',
        ]);

        $response->assertRedirect('/colaboradores');
        $this->assertDatabaseHas('colaboradores', ['num_empleado' => 'WEB-TEST-001']);

        // Limpieza
        Colaborador::where('num_empleado', 'WEB-TEST-001')->delete();
    }

    public function test_no_elimina_colaborador_activo_con_historial(): void
    {
        $colaborador = Colaborador::create([
            'hotel_id' => 1, 'departamento_id' => 1,
            'nombre' => 'Colaborador Activo', 'num_empleado' => 'COL-ACT-'.uniqid(),
            'estado' => 'activo',
        ]);
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'colaborador_id' => $colaborador->id,
            'num_inventario' => 'COL-ACTIVO-'.uniqid(), 'estado' => 'activo',
        ]);
        Asignacion::create([
            'activo_id' => $activo->id,
            'colaborador_id' => $colaborador->id,
            'fecha_asignacion' => date('Y-m-d'),
        ]);

        $this->delete(route('colaboradores.destroy', $colaborador))
            ->assertRedirect()
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('colaboradores', ['id' => $colaborador->id]);
    }

    public function test_elimina_colaborador_dado_de_baja_y_libera_sus_activos(): void
    {
        $colaborador = Colaborador::create([
            'hotel_id' => 1, 'departamento_id' => 1,
            'nombre' => 'Colaborador Baja', 'num_empleado' => 'COL-BAJA-'.uniqid(),
            'estado' => 'baja',
        ]);
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'colaborador_id' => $colaborador->id,
            'num_inventario' => 'COL-LIBERAR-'.uniqid(), 'estado' => 'activo',
        ]);
        $asignacion = Asignacion::create([
            'activo_id' => $activo->id,
            'colaborador_id' => $colaborador->id,
            'fecha_asignacion' => date('Y-m-d'),
        ]);

        $this->delete(route('colaboradores.destroy', $colaborador))
            ->assertRedirect(route('colaboradores.index'))
            ->assertSessionHas('exito', 'Colaborador eliminado.');

        $this->assertDatabaseMissing('colaboradores', ['id' => $colaborador->id]);
        $this->assertDatabaseMissing('asignaciones', ['id' => $asignacion->id]);
        $this->assertDatabaseHas('activos', [
            'id' => $activo->id,
            'colaborador_id' => null,
            'estado' => 'stock',
        ]);
    }

    public function test_flujo_asignar_devolver_baja(): void
    {
        $colab = Colaborador::create([
            'hotel_id' => 1, 'departamento_id' => 1,
            'nombre' => 'Flujo Test', 'num_empleado' => 'FLUJO-'.uniqid(),
        ]);
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'num_inventario' => 'FLUJO-'.uniqid(), 'estado' => 'stock',
        ]);

        // Asignar
        $this->post(route('activos.asignar', $activo), [
            'colaborador_id' => $colab->id,
            'fecha_asignacion' => date('Y-m-d'),
            'condicion_entrega' => 'bueno',
        ])->assertRedirect();
        $this->assertEquals('activo', $activo->fresh()->estado);
        $this->assertEquals($colab->id, $activo->fresh()->colaborador_id);

        // Devolver
        $this->post(route('activos.devolver', $activo), [
            'fecha_devolucion' => date('Y-m-d'),
            'condicion_retorno' => 'bueno',
        ])->assertRedirect();
        $this->assertEquals('stock', $activo->fresh()->estado);
        $this->assertNull($activo->fresh()->colaborador_id);

        // Baja
        $this->post(route('activos.baja', $activo), [
            'fecha_baja' => date('Y-m-d'),
            'motivo_baja' => 'Prueba',
        ])->assertRedirect();
        $this->assertEquals('baja', $activo->fresh()->estado);

        // Limpieza
        Asignacion::where('activo_id', $activo->id)->delete();
        Auditoria::where('activo_id', $activo->id)->delete();
        $activo->delete();
        $colab->delete();
    }

    public function test_generar_etiqueta_con_codigo_de_barras(): void
    {
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'num_inventario' => 'ETIQ-'.uniqid(), 'estado' => 'stock',
        ]);

        $response = $this->get(route('etiquetas.imprimir', $activo));

        $response->assertOk();
        $response->assertSee($activo->num_inventario);
        $response->assertSee('<svg', false); // el código de barras SVG está presente

        // Se registró en la bitácora de etiquetas
        $this->assertDatabaseHas('etiquetas', [
            'activo_id' => $activo->id,
            'tipo_impresion' => 'termica',
        ]);

        // Limpieza
        Etiqueta::where('activo_id', $activo->id)->delete();
        $activo->delete();
    }

    public function test_crud_catalogo_hotel(): void
    {
        $codigo = 'T'.rand(10, 99);

        // Crear
        $this->post(route('catalogos.hoteles.store'), [
            'nombre' => 'Hotel Test', 'codigo' => $codigo,
        ])->assertRedirect(route('catalogos.hoteles.index'));
        $this->assertDatabaseHas('hoteles', ['codigo' => $codigo]);

        $hotel = Hotel::where('codigo', $codigo)->first();

        // Editar
        $this->put(route('catalogos.hoteles.update', $hotel), [
            'nombre' => 'Hotel Editado', 'codigo' => $codigo, 'activo' => false,
        ])->assertRedirect(route('catalogos.hoteles.index'));
        $this->assertEquals('Hotel Editado', $hotel->fresh()->nombre);
        $this->assertFalse($hotel->fresh()->activo);

        // Eliminar
        $this->delete(route('catalogos.hoteles.destroy', $hotel))->assertRedirect();
        $this->assertDatabaseMissing('hoteles', ['codigo' => $codigo]);
    }

    public function test_escaneo_encuentra_activo(): void
    {
        $num = 'SCAN-'.uniqid();
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'num_inventario' => $num, 'estado' => 'stock',
        ]);

        // Escaneo por número de inventario → lo encuentra
        $this->get(route('escaneo.index', ['codigo' => $num]))
            ->assertOk()
            ->assertSee('Equipo encontrado')
            ->assertSee($num);

        // Quedó registrado en auditoría como 'escaneo'
        $this->assertDatabaseHas('auditoria', [
            'activo_id' => $activo->id,
            'accion' => 'escaneo',
        ]);

        // Escaneo de un código inexistente → no encontrado
        $this->get(route('escaneo.index', ['codigo' => 'NO-EXISTE-XYZ']))
            ->assertOk()
            ->assertSee('No se encontró');

        // Limpieza
        Auditoria::where('activo_id', $activo->id)->delete();
        $activo->delete();
    }

    public function test_crear_activo_desde_formulario(): void
    {
        $numInv = 'WEB-TEST-'.uniqid();

        $response = $this->post('/activos', [
            'tipo_activo_id' => 1,
            'hotel_id' => 1,
            'departamento_id' => 1,
            'num_inventario' => $numInv,
            'marca' => 'HP',
            'modelo' => 'EliteBook',
            'estado' => 'stock',
        ]);

        $response->assertRedirect('/activos');
        $this->assertDatabaseHas('activos', ['num_inventario' => $numInv]);

        // Limpieza
        Activo::where('num_inventario', $numInv)->delete();
    }

    public function test_no_elimina_activo_con_contrato_y_muestra_motivo_y_alternativa(): void
    {
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'num_inventario' => 'BLOQUEO-'.uniqid(), 'estado' => 'stock',
        ]);
        Contrato::create([
            'activo_id' => $activo->id,
            'tipo' => 'mantenimiento',
            'proveedor' => 'Proveedor de prueba',
            'fecha_inicio' => date('Y-m-d'),
        ]);

        $response = $this->delete(route('activos.destroy', $activo))
            ->assertRedirect()
            ->assertSessionHasErrors();

        $mensaje = $response->getSession()->get('errors')->getBag('default')->first();
        $this->assertStringContainsString('1 contrato(s)', $mensaje);
        $this->assertStringContainsString('Dar de baja', $mensaje);

        $this->assertDatabaseHas('activos', ['id' => $activo->id]);
    }

    public function test_api_responde_409_al_eliminar_activo_con_historial(): void
    {
        $colaborador = Colaborador::create([
            'hotel_id' => 1, 'departamento_id' => 1,
            'nombre' => 'Historial API', 'num_empleado' => 'API-'.uniqid(),
        ]);
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'num_inventario' => 'API-BLOQUEO-'.uniqid(), 'estado' => 'stock',
        ]);
        Asignacion::create([
            'activo_id' => $activo->id,
            'colaborador_id' => $colaborador->id,
            'fecha_asignacion' => date('Y-m-d'),
        ]);

        $this->deleteJson(route('api.inventario.activos.destroy', $activo))
            ->assertStatus(409)
            ->assertJsonPath('error', 'eliminacion_bloqueada')
            ->assertJsonPath('alternativa', fn (string $texto) => str_contains($texto, 'Dar de baja'));

        $this->assertDatabaseHas('activos', ['id' => $activo->id]);
    }

    public function test_elimina_activo_dado_de_baja_aunque_tenga_historial(): void
    {
        $colaborador = Colaborador::create([
            'hotel_id' => 1, 'departamento_id' => 1,
            'nombre' => 'Historial Baja', 'num_empleado' => 'BAJA-'.uniqid(),
        ]);
        $activo = Activo::create([
            'tipo_activo_id' => 1, 'hotel_id' => 1, 'departamento_id' => 1,
            'num_inventario' => 'BAJA-ELIMINAR-'.uniqid(), 'estado' => 'stock',
        ]);
        $asignacion = Asignacion::create([
            'activo_id' => $activo->id,
            'colaborador_id' => $colaborador->id,
            'fecha_asignacion' => date('Y-m-d'),
        ]);
        $etiqueta = Etiqueta::create([
            'activo_id' => $activo->id,
            'usuario_sistema_id' => auth()->id(),
            'tipo_impresion' => 'codigo_barras',
        ]);

        $this->post(route('activos.baja', $activo), [
            'fecha_baja' => date('Y-m-d'),
            'motivo_baja' => 'Fin de vida útil',
        ])->assertRedirect();

        $this->delete(route('activos.destroy', $activo))
            ->assertRedirect(route('activos.index'))
            ->assertSessionHas('exito', 'Activo eliminado.');

        $this->assertDatabaseMissing('activos', ['id' => $activo->id]);
        $this->assertDatabaseMissing('asignaciones', ['id' => $asignacion->id]);
        $this->assertDatabaseMissing('etiquetas', ['id' => $etiqueta->id]);
    }

    public function test_api_explica_por_que_no_elimina_catalogo_en_uso(): void
    {
        $this->deleteJson(route('api.inventario.hoteles.destroy', 1))
            ->assertStatus(409)
            ->assertJsonPath('error', 'eliminacion_bloqueada')
            ->assertJsonStructure(['message', 'alternativa']);

        $this->assertDatabaseHas('hoteles', ['id' => 1]);
    }

    public function test_usuario_actualiza_su_perfil(): void
    {
        $usuario = auth()->user();

        $this->put(route('perfil.update'), [
            'nombre' => 'Administrador Actualizado',
            'email' => $usuario->email,
            'telefono' => '+52 998 123 4567',
        ])->assertRedirect()->assertSessionHas('exito');

        $this->assertDatabaseHas('usuarios_sistema', [
            'id' => $usuario->id,
            'nombre' => 'Administrador Actualizado',
            'telefono' => '+52 998 123 4567',
        ]);
    }
}
