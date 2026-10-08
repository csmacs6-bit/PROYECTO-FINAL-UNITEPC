<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('transport:admin', function () {
    $name = $this->ask('Nombre completo');
    $username = mb_strtolower(trim((string) $this->ask('Usuario')));
    $password = $this->secret('Contraseña (mínimo 8 caracteres)');
    if (!$name || !$username || strlen((string) $password) < 8) {
        $this->error('Completa los datos y utiliza una contraseña de al menos 8 caracteres.');
        return 1;
    }
    if (DB::table('usuarios')->where('username', $username)->exists()) {
        $this->error('El usuario ya existe.');
        return 1;
    }
    DB::table('usuarios')->insert(['fullName' => $name, 'username' => $username, 'password' => Hash::make($password), 'role' => 'Administrador', 'status' => 'Activo', 'created_at' => now(), 'updated_at' => now()]);
    $this->info('Administrador creado.');
})->purpose('Crear el administrador inicial sin credenciales predeterminadas');

Artisan::command('transport:database', function () {
    $connection = config('database.connections.mysql');
    $database = $connection['database'];
    if (!preg_match('/\A[a-zA-Z0-9_]+\z/', $database)) {
        $this->error('El nombre de la base de datos solo puede contener letras, números y guiones bajos.');
        return 1;
    }
    $connection['database'] = null;
    config(['database.connections.setup_transport' => $connection]);
    DB::connection('setup_transport')->statement("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    DB::purge('setup_transport');
    $this->info('Base de datos preparada: '.$database);
})->purpose('Crear la base MySQL configurada sin borrar datos existentes');
