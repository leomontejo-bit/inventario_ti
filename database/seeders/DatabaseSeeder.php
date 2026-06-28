<?php

namespace Database\Seeders;

use App\Models\UsuarioSistema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('hoteles')->updateOrInsert([
            'codigo' => 'BP',
        ], [
            'nombre' => 'Bahia Principe',
            'direccion' => null,
            'activo' => true,
            'created_at' => now(),
        ]);

        DB::table('departamentos')->updateOrInsert([
            'nombre' => 'TI',
        ], [
            'activo' => true,
            'created_at' => now(),
        ]);

        DB::table('tipos_activo')->updateOrInsert([
            'nombre' => 'Laptop',
        ], [
            'categoria' => 'equipo_computo',
            'prefijo_codigo' => 'LAP',
            'activo' => true,
        ]);

        UsuarioSistema::query()->updateOrCreate([
            'email' => 'admin@inventario.test',
        ], [
            'nombre' => 'Administrador TI',
            'password_hash' => Hash::make('AdminInventario2026!'),
            'rol' => 'admin',
            'activo' => true,
        ]);
    }
}
