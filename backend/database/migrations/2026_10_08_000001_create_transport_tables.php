<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('fullName');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('camiones', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique();
            $table->string('brand');
            $table->string('model');
            $table->unsignedInteger('year');
            $table->string('vehicleType');
            $table->decimal('capacity', 14, 2);
            $table->string('gps')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('conductores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identity')->unique();
            $table->string('license')->unique();
            $table->string('category');
            $table->date('expiresAt');
            $table->string('phone');
            $table->foreignId('truck_id')->nullable()->constrained('camiones')->restrictOnDelete();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nit')->unique();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('contact');
            $table->timestamps();
        });
        Schema::create('cargas', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('type');
            $table->decimal('weight', 14, 2);
            $table->string('origin');
            $table->string('destination');
            $table->foreignId('client_id')->constrained('clientes')->restrictOnDelete();
            $table->decimal('freight', 14, 2);
            $table->date('departureDate');
            $table->date('arrivalDate')->nullable();
            $table->text('observations')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->foreignId('client_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('cargo_id')->constrained('cargas')->restrictOnDelete();
            $table->foreignId('truck_id')->constrained('camiones')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('conductores')->restrictOnDelete();
            $table->string('origin');
            $table->string('destination');
            $table->date('date');
            $table->date('arrivalDate')->nullable();
            $table->decimal('freight', 14, 2);
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('combustible', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('truck_id')->constrained('camiones')->restrictOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained('viajes')->restrictOnDelete();
            $table->decimal('liters', 14, 2);
            $table->decimal('price', 14, 2);
            $table->unsignedInteger('odometer');
            $table->string('location');
            $table->timestamps();
        });
        Schema::create('mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained('camiones')->restrictOnDelete();
            $table->string('description');
            $table->string('type');
            $table->date('date');
            $table->unsignedInteger('odometer');
            $table->decimal('cost', 14, 2);
            $table->string('workshop');
            $table->date('nextDate')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_tokens');
        Schema::dropIfExists('mantenimiento');
        Schema::dropIfExists('combustible');
        Schema::dropIfExists('viajes');
        Schema::dropIfExists('cargas');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('conductores');
        Schema::dropIfExists('camiones');
        Schema::dropIfExists('usuarios');
    }
};
