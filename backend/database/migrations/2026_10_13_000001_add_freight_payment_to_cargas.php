<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cargas', function (Blueprint $table) {
            $table->string('paymentStatus')->default('Pendiente');
            $table->date('paidAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cargas', fn (Blueprint $table) => $table->dropColumn(['paymentStatus', 'paidAt']));
    }
};
