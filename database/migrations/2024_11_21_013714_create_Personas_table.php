<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Personas', function (Blueprint $table) {
            $table->unsignedBigInteger('IdPersona', true);
            $table->char('CUIT', 11)->nullable()->index('uix_personas_cuit');
            $table->string('Apellido', 50)->nullable();
            $table->string('Nombre', 50)->nullable();
            $table->string('Nacionalidad', 100)->nullable();
            $table->string('Actividad', 150)->nullable();
            $table->string('Domicilio', 300)->nullable();
            $table->string('Email', 100)->nullable();
            $table->string('Telefono', 20)->nullable();
            $table->string('Movil', 20)->nullable();
            $table->char('SituacionFiscal', 1);
            $table->date('FNacimiento')->nullable();
            $table->string('DNI', 12)->nullable();
            $table->string('Alias', 100)->nullable();
            $table->string('CodPostal', 10)->nullable();
            $table->char('PEP', 1)->nullable();
            $table->char('EstadoPersona', 1)->default('A');
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Personas');
    }
};
