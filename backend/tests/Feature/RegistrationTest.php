<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
    }

    private function administrator(): string
    {
        DB::table('usuarios')->insert(['fullName' => 'Admin', 'username' => 'admin', 'password' => Hash::make('secure123'), 'role' => 'Administrador', 'status' => 'Activo', 'created_at' => now(), 'updated_at' => now()]);
        return $this->postJson('/api/login', ['username' => 'admin', 'password' => 'secure123'])->assertOk()->json('token');
    }

    public function test_registration_persists_and_lists_every_module_with_relations(): void
    {
        $this->withToken($this->administrator());
        $fixtures = [
            'usuarios' => ['fullName' => 'Operador', 'username' => 'operador', 'password' => 'secure123', 'password_confirmation' => 'secure123', 'role' => 'Operador', 'status' => 'Activo'],
            'camiones' => ['plate' => 'abc-123', 'brand' => 'Volvo', 'model' => 'FH', 'year' => 2020, 'vehicleType' => 'Tractocamión', 'capacity' => 30, 'status' => 'Disponible'],
            'conductores' => ['name' => 'Juan', 'identity' => '12345', 'license' => 'LIC123', 'category' => 'C', 'expiresAt' => '2027-10-08', 'phone' => '70000000', 'truck_id' => 1, 'status' => 'Activo'],
            'clientes' => ['name' => 'Cliente', 'nit' => '123456', 'phone' => '70000000', 'email' => 'cliente@example.com', 'address' => 'Cochabamba', 'contact' => 'Ana'],
            'cargas' => ['description' => 'Cemento', 'type' => 'General', 'weight' => 20, 'origin' => 'Cochabamba', 'destination' => 'La Paz', 'client_id' => 1, 'freight' => 5000, 'departureDate' => '2026-10-08', 'arrivalDate' => '2026-10-09', 'status' => 'Programada'],
            'viajes' => ['description' => 'Viaje de cemento', 'client_id' => 1, 'cargo_id' => 1, 'truck_id' => 1, 'driver_id' => 1, 'origin' => 'Cochabamba', 'destination' => 'La Paz', 'date' => '2026-10-08', 'freight' => 5000, 'status' => 'Programado'],
            'combustible' => ['date' => '2026-10-08', 'truck_id' => 1, 'trip_id' => 1, 'liters' => 100, 'price' => 3.74, 'odometer' => 10000, 'location' => 'Estación'],
            'mantenimiento' => ['truck_id' => 1, 'description' => 'Cambio de aceite', 'type' => 'Preventivo', 'date' => '2026-10-08', 'odometer' => 10000, 'cost' => 300, 'workshop' => 'Taller', 'nextDate' => '2026-11-08', 'status' => 'Realizado'],
        ];
        foreach ($fixtures as $module => $payload) {
            $response = $this->postJson('/api/'.$module, $payload)->assertCreated()->assertJsonMissingPath('data.password');
            $id = $response->json('data.id');
            $this->assertDatabaseHas($module, ['id' => $id]);
            $this->getJson('/api/'.$module)->assertOk()->assertJsonFragment(['id' => $id]);
        }
        $trip = $fixtures['viajes'];
        $this->postJson('/api/viajes', [...$trip, 'arrivalDate' => '2026-10-01'])->assertUnprocessable()->assertJsonValidationErrors('arrivalDate');
        $this->postJson('/api/viajes', [...$trip, 'freight' => -1])->assertUnprocessable()->assertJsonValidationErrors('freight');
        $this->putJson('/api/viajes/1', [...$trip, 'status' => 'Completado'])->assertUnprocessable()->assertJsonValidationErrors('arrivalDate');
        DB::table('camiones')->where('id', 1)->update(['capacity' => 10]);
        $this->postJson('/api/viajes', $trip)->assertUnprocessable()->assertJsonValidationErrors('truck_id');
        $this->putJson('/api/viajes/1', $trip)->assertUnprocessable()->assertJsonValidationErrors('truck_id');
        DB::table('camiones')->where('id', 1)->update(['capacity' => 30, 'status' => 'Mantenimiento']);
        $this->postJson('/api/viajes', $trip)->assertUnprocessable()->assertJsonValidationErrors('truck_id');
        DB::table('camiones')->where('id', 1)->update(['status' => 'Disponible']);
        DB::table('conductores')->where('id', 1)->update(['status' => 'Inactivo']);
        $this->postJson('/api/viajes', $trip)->assertUnprocessable()->assertJsonValidationErrors('driver_id');
        DB::table('conductores')->where('id', 1)->update(['status' => 'Activo', 'expiresAt' => '2026-10-01']);
        $this->putJson('/api/viajes/1', $trip)->assertUnprocessable()->assertJsonValidationErrors('driver_id');
        DB::table('conductores')->where('id', 1)->update(['expiresAt' => '2027-10-08']);
        DB::table('clientes')->insert([...$fixtures['clientes'], 'nit' => 'SECOND']);
        $this->postJson('/api/viajes', [...$trip, 'client_id' => 2])->assertUnprocessable()->assertJsonValidationErrors('cargo_id');
        $this->putJson('/api/viajes/1', [...$trip, 'client_id' => 2])->assertUnprocessable()->assertJsonValidationErrors('cargo_id');
        $this->putJson('/api/viajes/1', [...$trip, 'status' => 'En transito'])->assertOk()->assertJsonPath('data.status', 'En transito');
        $this->putJson('/api/viajes/1', [...$trip, 'status' => 'Completado', 'arrivalDate' => '2026-10-09'])->assertOk()->assertJsonPath('data.status', 'Completado');
        foreach ($fixtures as $module => $payload) {
            if ($module === 'usuarios') unset($payload['password'], $payload['password_confirmation']);
            $id = $module === 'usuarios' ? 2 : 1;
            $this->putJson('/api/'.$module.'/'.$id, $payload)->assertOk()->assertJsonPath('data.id', $id)->assertJsonMissingPath('data.password');
        }
        $this->putJson('/api/fletes/1/cobro', ['paymentStatus' => 'Cobrado', 'paidAt' => '2026-10-10'])->assertUnprocessable();
        $this->putJson('/api/cargas/1', [...$fixtures['cargas'], 'status' => 'Entregada'])->assertOk()->assertJsonPath('data.paymentStatus', 'Pendiente');
        $this->putJson('/api/fletes/1/cobro', ['paymentStatus' => 'Cobrado'])->assertUnprocessable()->assertJsonValidationErrors('paidAt');
        $this->putJson('/api/fletes/1/cobro', ['paymentStatus' => 'Otro'])->assertUnprocessable();
        $this->putJson('/api/fletes/999/cobro', ['paymentStatus' => 'Pendiente'])->assertNotFound();
        $this->putJson('/api/fletes/1/cobro', ['paymentStatus' => 'Cobrado', 'paidAt' => '2026-10-10'])->assertOk()->assertJsonPath('data.paymentStatus', 'Cobrado');
        $this->getJson('/api/cargas')->assertOk()->assertJsonFragment(['paymentStatus' => 'Cobrado', 'paidAt' => '2026-10-10']);
        $this->putJson('/api/cargas/1', [...$fixtures['cargas'], 'status' => 'Entregada'])->assertOk()->assertJsonPath('data.paymentStatus', 'Cobrado');
        $this->putJson('/api/fletes/1/cobro', ['paymentStatus' => 'Pendiente'])->assertOk()->assertJsonPath('data.paidAt', null);
        $this->assertDatabaseHas('cargas', ['id' => 1, 'paymentStatus' => 'Pendiente', 'paidAt' => null]);
        $this->assertDatabaseHas('camiones', ['plate' => 'ABC-123']);
        $this->assertTrue(Hash::check('secure123', DB::table('usuarios')->where('username', 'operador')->value('password')));
        $this->postJson('/api/camiones', $fixtures['camiones'])->assertUnprocessable()->assertJsonValidationErrors('plate');
        $this->postJson('/api/viajes', [...$fixtures['viajes'], 'truck_id' => 999])->assertUnprocessable()->assertJsonValidationErrors('truck_id');
        $this->postJson('/api/cargas', [...$fixtures['cargas'], 'arrivalDate' => '2026-10-01'])->assertUnprocessable()->assertJsonValidationErrors('arrivalDate');
        $this->postJson('/api/combustible', [...$fixtures['combustible'], 'liters' => -1])->assertUnprocessable()->assertJsonValidationErrors('liters');
        $this->postJson('/api/clientes', [...$fixtures['clientes'], 'nit' => '999', 'email' => 'invalid'])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->deleteJson('/api/clientes/1')->assertConflict();
        $this->deleteJson('/api/usuarios/1')->assertUnprocessable();
        $this->putJson('/api/usuarios/1', ['fullName' => 'Admin', 'username' => 'admin', 'role' => 'Usuario', 'status' => 'Activo'])->assertUnprocessable();
        $this->putJson('/api/camiones/999', $fixtures['camiones'])->assertNotFound();
        $this->deleteJson('/api/camiones/999')->assertNotFound();
        foreach (array_reverse(array_keys($fixtures)) as $module) {
            $id = $module === 'usuarios' ? 2 : 1;
            $this->deleteJson('/api/'.$module.'/'.$id)->assertNoContent();
            $this->assertDatabaseMissing($module, ['id' => $id]);
        }
        $this->getJson('/api/not-a-module')->assertNotFound();
        $this->postJson('/api/logout')->assertNoContent();
        $this->getJson('/api/camiones')->assertUnauthorized();
    }

    public function test_public_account_login_and_permissions(): void
    {
        $this->postJson('/api/clientes', [])->assertUnauthorized();
        $payload = ['fullName' => 'Nuevo', 'username' => 'NUEVO', 'password' => 'secure123', 'password_confirmation' => 'secure123', 'role' => 'Administrador'];
        $this->postJson('/api/register', $payload)->assertCreated()->assertJsonPath('data.role', 'Usuario')->assertJsonMissingPath('data.password');
        $this->assertDatabaseHas('usuarios', ['username' => 'nuevo']);
        $this->postJson('/api/register', $payload)->assertUnprocessable()->assertJsonValidationErrors('username');
        $this->postJson('/api/register', [...$payload, 'username' => 'bad', 'password_confirmation' => 'other'])->assertUnprocessable()->assertJsonValidationErrors('password');
        $token = $this->postJson('/api/login', ['username' => 'nuevo', 'password' => 'secure123'])->assertOk()->json('token');
        $this->withToken($token)->getJson('/api/usuarios')->assertForbidden();
        $this->postJson('/api/usuarios', $payload)->assertForbidden();
        $this->putJson('/api/usuarios/1', $payload)->assertForbidden();
        $this->deleteJson('/api/usuarios/1')->assertForbidden();
        DB::table('access_tokens')->update(['expires_at' => now()->subMinute()]);
        $this->getJson('/api/clientes')->assertUnauthorized();
    }

    public function test_driver_cannot_create_records_and_inactive_account_cannot_login(): void
    {
        $this->postJson('/api/register', ['fullName' => 'Chofer', 'username' => 'chofer', 'password' => 'secure123', 'password_confirmation' => 'secure123', 'role' => 'Chofer'])->assertCreated();
        $token = $this->postJson('/api/login', ['username' => 'chofer', 'password' => 'secure123'])->assertOk()->json('token');
        $this->withToken($token)->postJson('/api/camiones', [])->assertForbidden();
        $this->putJson('/api/camiones/1', [])->assertForbidden();
        $this->deleteJson('/api/camiones/1')->assertForbidden();
        DB::table('usuarios')->update(['status' => 'Inactivo']);
        $this->postJson('/api/login', ['username' => 'chofer', 'password' => 'secure123'])->assertUnprocessable();
    }
}
