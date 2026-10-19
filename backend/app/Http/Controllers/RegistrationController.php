<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    private function fields(string $module): array
    {
        $schema = json_decode(file_get_contents(base_path('../shared/registrations.json')), true, 512, JSON_THROW_ON_ERROR);
        abort_unless(isset($schema[$module]), 404);
        return $schema[$module];
    }

    private function authorizeModule(Request $request, string $module): void
    {
        $this->fields($module);
        abort_if($module === 'usuarios' && $request->attributes->get('transportUser')->role !== 'Administrador', 403, 'Solo el administrador puede gestionar usuarios.');
    }

    private function validateData(Request $request, string $module, ?int $id = null): array
    {
        if ($module === 'usuarios') {
            $request->merge(['username' => mb_strtolower(trim((string) $request->input('username')))]);
        }
        if ($module === 'camiones') {
            $request->merge(['plate' => mb_strtoupper(trim((string) $request->input('plate')))]);
        }
        $rules = [];
        $labels = [];
        foreach ($this->fields($module) as $field) {
            $name = $field['name'];
            $labels[$name] = $field['label'];
            $rules[$name] = [($field['required'] && !($id && $name === 'password')) ? 'required' : 'nullable'];
            switch ($field['type']) {
                case 'number':
                    $rules[$name][] = ($field['integer'] ?? false) || $name === 'year' ? 'integer' : 'numeric';
                    $rules[$name][] = 'min:'.($field['min'] ?? 0);
                    $rules[$name][] = 'max:'.($field['max'] ?? 999999999999.99);
                    break;
                case 'relation':
                    $rules[$name][] = 'integer';
                    $rules[$name][] = Rule::exists($field['resource'], 'id');
                    break;
                case 'date':
                    $rules[$name][] = 'date_format:Y-m-d';
                    if (isset($field['after'])) $rules[$name][] = 'after_or_equal:'.$field['after'];
                    break;
                case 'select':
                    $rules[$name][] = Rule::in($field['options']);
                    break;
                default:
                    $rules[$name][] = 'string';
                    $rules[$name][] = $field['type'] === 'textarea' ? 'max:5000' : 'max:255';
                    if ($field['type'] === 'email') $rules[$name][] = 'email';
                    if ($field['type'] === 'password') {
                        $rules[$name][] = 'min:8';
                        $rules[$name][] = 'confirmed';
                    }
            }
            if ($field['unique'] ?? false) $rules[$name][] = Rule::unique($module, $name)->ignore($id);
        }
        if ($module === 'viajes') $rules['arrivalDate'][] = 'required_if:status,Completado';
        $validator = Validator::make($request->all(), $rules, [
            'required' => 'El campo :attribute es obligatorio.',
            'unique' => 'El valor de :attribute ya está registrado.',
            'exists' => 'Selecciona un registro válido para :attribute.',
            'min' => 'El campo :attribute debe tener un valor mínimo de :min.',
            'max' => 'El campo :attribute supera el máximo de :max.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'email' => 'Ingresa un correo electrónico válido.',
            'after_or_equal' => 'La fecha de :attribute debe ser igual o posterior a :date.',
            'in' => 'Selecciona una opción válida para :attribute.',
        ], $labels);
        if ($module === 'viajes') {
            $validator->after(function ($validator) use ($request) {
                if ($validator->errors()->isNotEmpty()) return;
                $cargo = DB::table('cargas')->find($request->input('cargo_id'));
                $truck = DB::table('camiones')->find($request->input('truck_id'));
                $driver = DB::table('conductores')->find($request->input('driver_id'));
                if ($cargo->client_id != $request->input('client_id')) {
                    $validator->errors()->add('cargo_id', 'La carga seleccionada pertenece a otro cliente.');
                }
                if ($cargo->weight > $truck->capacity) {
                    $validator->errors()->add('truck_id', 'El peso de la carga supera la capacidad del camión.');
                }
                if (in_array($request->input('status'), ['Programado', 'En transito'])) {
                    if ($truck->status === 'Mantenimiento') $validator->errors()->add('truck_id', 'El camión está en mantenimiento.');
                    if ($driver->status !== 'Activo') $validator->errors()->add('driver_id', 'El conductor debe estar activo.');
                    if ($driver->expiresAt < $request->input('date')) $validator->errors()->add('driver_id', 'La licencia vence antes de la salida del viaje.');
                }
            });
        }
        return $validator->validate();
    }

    private function insert(string $module, array $data): array
    {
        if (isset($data['password'])) $data['password'] = Hash::make($data['password']);
        $data['created_at'] = $data['updated_at'] = now();
        $id = DB::table($module)->insertGetId($data);
        return $this->safe(DB::table($module)->find($id));
    }

    private function safe(object $record): array
    {
        $data = (array) $record;
        unset($data['password']);
        return $data;
    }

    public function updateFreightPayment(Request $request, int $id)
    {
        $data = $request->validate([
            'paymentStatus' => ['required', Rule::in(['Pendiente', 'Cobrado'])],
            'paidAt' => ['nullable', 'date_format:Y-m-d', 'required_if:paymentStatus,Cobrado'],
        ]);
        return DB::transaction(function () use ($id, $data) {
            $cargo = DB::table('cargas')->where('id', $id)->lockForUpdate()->first();
            abort_unless($cargo, 404, 'La carga ya no existe.');
            abort_unless($cargo->status === 'Entregada', 422, 'Solo puedes registrar el cobro de cargas entregadas.');
            DB::table('cargas')->where('id', $id)->update([
                'paymentStatus' => $data['paymentStatus'],
                'paidAt' => $data['paymentStatus'] === 'Cobrado' ? $data['paidAt'] : null,
                'updated_at' => now(),
            ]);
            return response()->json(['data' => $this->safe(DB::table('cargas')->find($id))]);
        });
    }

    public function index(Request $request, string $module)
    {
        $this->authorizeModule($request, $module);
        return response()->json(['data' => DB::table($module)->orderByDesc('id')->get()->map(fn ($row) => $this->safe($row))]);
    }

    public function store(Request $request, string $module)
    {
        $this->authorizeModule($request, $module);
        $data = $this->validateData($request, $module);
        if ($module === 'viajes') {
            $cargo = DB::table('cargas')->find($data['cargo_id']);
            abort_unless($cargo->client_id == $data['client_id'], 422, 'La carga seleccionada pertenece a otro cliente.');
        }
        return response()->json(['message' => 'Registro guardado correctamente.', 'data' => $this->insert($module, $data)], 201);
    }

    public function update(Request $request, string $module, int $id)
    {
        $this->authorizeModule($request, $module);
        abort_unless(DB::table($module)->find($id), 404, 'El registro ya no existe.');
        return DB::transaction(function () use ($request, $module, $id) {
            $data = $this->validateData($request, $module, $id);
            $this->protectOwnAccount($request, $module, $id, $data);
            if ($module === 'viajes') {
                $cargo = DB::table('cargas')->find($data['cargo_id']);
                abort_unless($cargo->client_id == $data['client_id'], 422, 'La carga seleccionada pertenece a otro cliente.');
            }
            if ($module === 'cargas') {
                abort_if(DB::table('viajes')->where('cargo_id', $id)->where('client_id', '!=', $data['client_id'])->exists(), 422, 'Esta carga tiene viajes asociados a su cliente actual.');
            }
            if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
            else unset($data['password']);
            $data['updated_at'] = now();
            DB::table($module)->where('id', $id)->update($data);
            return response()->json(['data' => $this->safe(DB::table($module)->find($id)), 'message' => 'Registro actualizado correctamente.']);
        });
    }

    private function protectOwnAccount(Request $request, string $module, int $id, array $data): void
    {
        if ($module !== 'usuarios' || $request->attributes->get('transportUser')->id != $id) return;
        abort_if(($data['role'] ?? '') !== 'Administrador' || ($data['status'] ?? '') !== 'Activo', 422, 'No puedes eliminar, desactivar ni quitar el rol de administrador de tu propia cuenta.');
    }

    public function destroy(Request $request, string $module, int $id)
    {
        $this->authorizeModule($request, $module);
        return DB::transaction(function () use ($request, $module, $id) {
            abort_unless(DB::table($module)->find($id), 404, 'El registro ya no existe.');
            $this->protectOwnAccount($request, $module, $id, []);
            $schema = json_decode(file_get_contents(base_path('../shared/registrations.json')), true, 512, JSON_THROW_ON_ERROR);
            foreach ($schema as $related => $fields) {
                foreach ($fields as $field) {
                    if (($field['resource'] ?? null) === $module) {
                        abort_if(DB::table($related)->where($field['name'], $id)->exists(), 409, 'No puedes eliminar este registro porque tiene registros relacionados en '.$related.'.');
                    }
                }
            }
            try {
                DB::table($module)->where('id', $id)->delete();
            } catch (\Illuminate\Database\QueryException $error) {
                if (in_array((string) $error->getCode(), ['23000', '23503'])) abort(409, 'No puedes eliminar este registro porque tiene registros relacionados.');
                throw $error;
            }
            return response()->noContent();
        });
    }

    public function register(Request $request)
    {
        // Public registration creates ordinary accounts; administrators create privileged accounts.
        $request->merge(['role' => $request->input('role') === 'Chofer' ? 'Chofer' : 'Usuario', 'status' => 'Activo']);
        return response()->json(['data' => $this->insert('usuarios', $this->validateData($request, 'usuarios'))], 201);
    }

    public function login(Request $request)
    {
        $request->validate(['username' => 'required|string', 'password' => 'required|string']);
        $user = DB::table('usuarios')->where('username', mb_strtolower(trim($request->username)))->first();
        abort_unless($user && Hash::check($request->password, $user->password) && $user->status === 'Activo', 422, 'Usuario o contraseña incorrectos, o cuenta inactiva.');
        $token = bin2hex(random_bytes(32));
        DB::table('access_tokens')->where('expires_at', '<=', now())->delete();
        DB::table('access_tokens')->insert(['user_id' => $user->id, 'token' => hash('sha256', $token), 'expires_at' => now()->addHours(12)]);
        return response()->json(['user' => $this->safe($user), 'token' => $token]);
    }

    public function logout(Request $request)
    {
        if ($request->bearerToken()) DB::table('access_tokens')->where('token', hash('sha256', $request->bearerToken()))->delete();
        return response()->noContent();
    }
}
