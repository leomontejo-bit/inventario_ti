<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios_sistema', function (Blueprint $table): void {
            if (! Schema::hasColumn('usuarios_sistema', 'telefono')) {
                $table->string('telefono', 30)->nullable()->after('email');
            }

            if (! Schema::hasColumn('usuarios_sistema', 'foto_perfil')) {
                $table->string('foto_perfil')->nullable()->after('telefono');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios_sistema', function (Blueprint $table): void {
            $columnas = array_filter(
                ['telefono', 'foto_perfil'],
                fn (string $columna) => Schema::hasColumn('usuarios_sistema', $columna),
            );

            if ($columnas) {
                $table->dropColumn($columnas);
            }
        });
    }
};
