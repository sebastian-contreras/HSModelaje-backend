<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Cajas', function (Blueprint $table) {
            $table->unsignedInteger('IdCaja', true);
            $table->unsignedInteger('NumeroCaja')->nullable();
            $table->char('Tamaño', 4)->nullable();
            $table->string('Ubicacion', 250)->nullable();
            $table->unsignedInteger('Fila')->nullable();
            $table->unsignedInteger('Columna')->nullable();
            $table->text('Observaciones')->nullable();
            $table->char('EstadoCaja', 1)->default('A');
            $table->timestamps();
            $table->softDeletes(); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Cajas');
    }
};
