<?php

namespace Tests\Feature;

use App\Models\Activo;
use App\Models\Colaborador;
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
        \App\Models\Asignacion::where('activo_id', $activo->id)->delete();
        \App\Models\Auditoria::where('activo_id', $activo->id)->delete();
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
        \App\Models\Etiqueta::where('activo_id', $activo->id)->delete();
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

        $hotel = \App\Models\Hotel::where('codigo', $codigo)->first();

        // Editar
        $this->put(route('catalogos.hoteles.update', $hotel), [
            'nombre' => 'Hotel Editado', 'codigo' => $codigo,
        ])->assertRedirect(route('catalogos.hoteles.index'));
        $this->assertEquals('Hotel Editado', $hotel->fresh()->nombre);

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
        \App\Models\Auditoria::where('activo_id', $activo->id)->delete();
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
}
