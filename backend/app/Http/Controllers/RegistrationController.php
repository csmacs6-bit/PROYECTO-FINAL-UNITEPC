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

    private function validateData(Request $request, string $module): array
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
            $rules[$name] = [$field['required'] ? 'required' : 'nullable'];
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
            if ($field['unique'] ?? false) $rules[$name][] = Rule::unique($module, $name);
        }
        return Validator::make($request->all(), $rules, [
            'required' => 'El campo :attribute es obligatorio.',
            'unique' => 'El valor de :attribute ya está registrado.',
            'exists' => 'Selecciona un registro válido para :attribute.',
            'min' => 'El campo :attribute debe tener un valor mínimo de :min.',
            'max' => 'El campo :attribute supera el máximo de :max.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'email' => 'Ingresa un correo electrónico válido.',
            'after_or_equal' => 'La fecha de :attribute debe ser igual o posterior a :date.',
            'in' => 'Selecciona una opción válida para :attribute.',
        ], $labels)->validate();
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
