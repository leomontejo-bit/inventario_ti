<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hoteles')) {
            Schema::create('hoteles', function (Blueprint $table): void {
                $table->id();
                $table->string('nombre', 100);
                $table->string('codigo', 20)->unique();
                $table->string('direccion', 200)->nullable();
                $table->boolean('activo')->default(true)->index();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('departamentos')) {
            Schema::create('departamentos', function (Blueprint $table): void {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->boolean('activo')->default(true)->index();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('tipos_activo')) {
            Schema::create('tipos_activo', function (Blueprint $table): void {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->string('categoria', 50)->default('otro')->index();
                $table->string('prefijo_codigo', 10)->nullable();
                $table->boolean('activo')->default(true)->index();
            });
        }

        if (! Schema::hasTable('colaboradores')) {
            Schema::create('colaboradores', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('hotel_id')->constrained('hoteles')->restrictOnDelete();
                $table->foreignId('departamento_id')->constrained('departamentos')->restrictOnDelete();
                $table->string('nombre', 200);
                $table->string('num_empleado', 50)->unique();
                $table->string('email_corporativo', 150)->nullable();
                $table->string('usuario_ad', 100)->nullable();
                $table->string('puesto', 150)->nullable();
                $table->string('estado', 30)->default('activo')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activos')) {
            Schema::create('activos', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('tipo_activo_id')->constrained('tipos_activo')->restrictOnDelete();
                $table->foreignId('hotel_id')->constrained('hoteles')->restrictOnDelete();
                $table->foreignId('departamento_id')->constrained('departamentos')->restrictOnDelete();
                $table->foreignId('colaborador_id')->nullable()->constrained('colaboradores')->nullOnDelete();
                $table->string('num_inventario', 50)->unique();
                $table->string('codigo_interno_ti', 50)->nullable();
                $table->string('codigo_barras', 100)->nullable()->unique();
                $table->string('num_serie', 100)->nullable();
                $table->string('nombre_equipo', 150)->nullable();
                $table->string('marca', 100)->nullable();
                $table->string('modelo', 150)->nullable();
                $table->string('procesador', 150)->nullable();
                $table->decimal('ram_gb', 8, 2)->nullable();
                $table->string('almacenamiento', 100)->nullable();
                $table->string('sistema_operativo', 150)->nullable();
                $table->string('direccion_ip', 45)->nullable();
                $table->string('direccion_mac', 17)->nullable();
                $table->string('estado', 30)->default('stock')->index();
                $table->date('fecha_adquisicion')->nullable();
                $table->date('fecha_baja')->nullable();
                $table->string('motivo_baja', 255)->nullable();
                $table->decimal('valor_adquisicion', 12, 2)->nullable();
                $table->text('observaciones')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('asignaciones')) {
            Schema::create('asignaciones', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('activo_id')->constrained('activos')->cascadeOnDelete();
                $table->foreignId('colaborador_id')->constrained('colaboradores')->restrictOnDelete();
                $table->foreignId('usuario_sistema_id')->nullable()->constrained('usuarios_sistema')->nullOnDelete();
                $table->date('fecha_asignacion');
                $table->date('fecha_devolucion')->nullable();
                $table->string('motivo_devolucion', 255)->nullable();
                $table->string('condicion_entrega', 30)->default('bueno');
                $table->string('condicion_retorno', 30)->nullable();
                $table->text('notas')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('licencias_software')) {
            Schema::create('licencias_software', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('activo_id')->nullable()->constrained('activos')->nullOnDelete();
                $table->string('nombre_software', 200);
                $table->string('version', 50)->nullable();
                $table->string('fabricante', 100)->nullable();
                $table->string('tipo_licencia', 30)->nullable();
                $table->string('clave_producto', 255)->nullable();
                $table->unsignedInteger('num_licencias')->default(1);
                $table->date('fecha_adquisicion')->nullable();
                $table->date('fecha_vencimiento')->nullable();
                $table->string('proveedor', 150)->nullable();
                $table->decimal('costo', 12, 2)->nullable();
                $table->string('estado', 30)->default('activa')->index();
                $table->text('notas')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('contratos')) {
            Schema::create('contratos', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('activo_id')->constrained('activos')->cascadeOnDelete();
                $table->string('tipo', 30);
                $table->string('proveedor', 150);
                $table->string('num_contrato', 100)->nullable();
                $table->string('contacto_proveedor', 150)->nullable();
                $table->string('telefono_proveedor', 50)->nullable();
                $table->date('fecha_inicio');
                $table->date('fecha_fin')->nullable();
                $table->decimal('monto', 12, 2)->nullable();
                $table->string('moneda', 3)->default('MXN');
                $table->string('estado', 30)->default('vigente')->index();
                $table->text('notas')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('etiquetas')) {
            Schema::create('etiquetas', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('activo_id')->constrained('activos')->cascadeOnDelete();
                $table->foreignId('usuario_sistema_id')->nullable()->constrained('usuarios_sistema')->nullOnDelete();
                $table->string('tipo_impresion', 30);
                $table->timestamp('fecha_generacion')->useCurrent();
                $table->json('datos_etiqueta')->nullable();
            });
        }

        if (! Schema::hasTable('auditoria')) {
            Schema::create('auditoria', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('activo_id')->nullable()->constrained('activos')->nullOnDelete();
                $table->foreignId('usuario_sistema_id')->nullable()->constrained('usuarios_sistema')->nullOnDelete();
                $table->string('tabla_afectada', 100);
                $table->unsignedBigInteger('registro_id');
                $table->string('accion', 50)->index();
                $table->json('valores_anteriores')->nullable();
                $table->json('valores_nuevos')->nullable();
                $table->string('ip_cliente', 45)->nullable();
                $table->timestamp('fecha')->useCurrent();
            });
        }

        $this->crearVistas();
    }

    public function down(): void
    {
        foreach (['v_activos_detalle', 'v_activos_sin_asignar', 'v_contratos_por_vencer', 'v_licencias_por_vencer'] as $vista) {
            DB::statement("DROP VIEW IF EXISTS {$vista}");
        }
    }

    private function crearVistas(): void
    {
        foreach (['v_activos_detalle', 'v_activos_sin_asignar', 'v_contratos_por_vencer', 'v_licencias_por_vencer'] as $vista) {
            DB::statement("DROP VIEW IF EXISTS {$vista}");
        }

        DB::statement($this->sqlVistaActivosDetalle());
        DB::statement($this->sqlVistaActivosSinAsignar());
        DB::statement($this->sqlVistaLicenciasPorVencer());
        DB::statement($this->sqlVistaContratosPorVencer());
    }

    private function sqlVistaActivosDetalle(): string
    {
        return <<<'SQL'
CREATE VIEW v_activos_detalle AS
SELECT
    a.*,
    ta.nombre AS tipo_nombre,
    ta.categoria AS tipo_categoria,
    h.nombre AS hotel_nombre,
    h.codigo AS hotel_codigo,
    d.nombre AS departamento_nombre,
    c.nombre AS colaborador_nombre,
    c.num_empleado AS colaborador_num_empleado
FROM activos a
LEFT JOIN tipos_activo ta ON ta.id = a.tipo_activo_id
LEFT JOIN hoteles h ON h.id = a.hotel_id
LEFT JOIN departamentos d ON d.id = a.departamento_id
LEFT JOIN colaboradores c ON c.id = a.colaborador_id
SQL;
    }

    private function sqlVistaActivosSinAsignar(): string
    {
        return <<<'SQL'
CREATE VIEW v_activos_sin_asignar AS
SELECT *
FROM activos
WHERE colaborador_id IS NULL
  AND estado = 'stock'
SQL;
    }

    private function sqlVistaLicenciasPorVencer(): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return <<<'SQL'
CREATE VIEW v_licencias_por_vencer AS
SELECT
    *,
    CAST(julianday(fecha_vencimiento) - julianday('now') AS INTEGER) AS dias_restantes
FROM licencias_software
WHERE estado = 'activa'
  AND fecha_vencimiento IS NOT NULL
  AND CAST(julianday(fecha_vencimiento) - julianday('now') AS INTEGER) BETWEEN 0 AND 60
SQL;
        }

        return <<<'SQL'
CREATE VIEW v_licencias_por_vencer AS
SELECT
    *,
    DATEDIFF(fecha_vencimiento, CURRENT_DATE) AS dias_restantes
FROM licencias_software
WHERE estado = 'activa'
  AND fecha_vencimiento IS NOT NULL
  AND DATEDIFF(fecha_vencimiento, CURRENT_DATE) BETWEEN 0 AND 60
SQL;
    }

    private function sqlVistaContratosPorVencer(): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return <<<'SQL'
CREATE VIEW v_contratos_por_vencer AS
SELECT
    *,
    CAST(julianday(fecha_fin) - julianday('now') AS INTEGER) AS dias_restantes
FROM contratos
WHERE estado = 'vigente'
  AND fecha_fin IS NOT NULL
  AND CAST(julianday(fecha_fin) - julianday('now') AS INTEGER) BETWEEN 0 AND 60
SQL;
        }

        return <<<'SQL'
CREATE VIEW v_contratos_por_vencer AS
SELECT
    *,
    DATEDIFF(fecha_fin, CURRENT_DATE) AS dias_restantes
FROM contratos
WHERE estado = 'vigente'
  AND fecha_fin IS NOT NULL
  AND DATEDIFF(fecha_fin, CURRENT_DATE) BETWEEN 0 AND 60
SQL;
    }
};
