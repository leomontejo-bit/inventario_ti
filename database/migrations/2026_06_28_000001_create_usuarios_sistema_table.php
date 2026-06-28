<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('usuarios_sistema')) {
            Schema::create('usuarios_sistema', function (Blueprint $table): void {
                $table->id();
                $table->string('nombre', 120);
                $table->string('email', 150)->unique();
                $table->string('password_hash');
                $table->string('rol', 30)->default('tecnico')->index();
                $table->boolean('activo')->default(true)->index();
                $table->timestamp('ultimo_acceso')->nullable();
                $table->rememberToken();
                $table->timestamp('created_at')->nullable();
            });

            return;
        }

        Schema::table('usuarios_sistema', function (Blueprint $table): void {
            if (! Schema::hasColumn('usuarios_sistema', 'nombre')) {
                $table->string('nombre', 120)->after('id');
            }

            if (! Schema::hasColumn('usuarios_sistema', 'email')) {
                $table->string('email', 150)->after('nombre')->unique();
            }

            if (! Schema::hasColumn('usuarios_sistema', 'password_hash')) {
                $table->string('password_hash')->after('email');
            }

            if (! Schema::hasColumn('usuarios_sistema', 'rol')) {
                $table->string('rol', 30)->default('tecnico')->after('password_hash')->index();
            }

            if (! Schema::hasColumn('usuarios_sistema', 'activo')) {
                $table->boolean('activo')->default(true)->after('rol')->index();
            }

            if (! Schema::hasColumn('usuarios_sistema', 'ultimo_acceso')) {
                $table->timestamp('ultimo_acceso')->nullable()->after('activo');
            }

            if (! Schema::hasColumn('usuarios_sistema', 'remember_token')) {
                $table->rememberToken()->after('ultimo_acceso');
            }

            if (! Schema::hasColumn('usuarios_sistema', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('remember_token');
            }
        });
    }

    public function down(): void
    {
        // No eliminamos la tabla porque puede existir con datos reales previos a esta migracion.
    }
};
