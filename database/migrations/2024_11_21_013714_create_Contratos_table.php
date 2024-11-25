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
        Schema::create('Contratos', function (Blueprint $table) {
            $table->unsignedBigInteger('IdContratos', true);
            $table->unsignedInteger('IdCaja')->index('ref43');
            $table->char('TipoApertura', 1);
            $table->unsignedInteger('PeriodoContratacion')->nullable();
            $table->char('AvisoIngreso', 1)->nullable();
            $table->unsignedInteger('FrecuenciaUso')->nullable();
            $table->char('Titularidad', 1)->nullable();
            $table->char('TipoUso', 1)->nullable();
            $table->unsignedBigInteger('IdFormaDePago')->nullable()->index('ref95');
            $table->timestamps();

            $table->index(['IdContratos', 'IdCaja'],'uix_Contratos_Caja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Contratos');
    }
};
