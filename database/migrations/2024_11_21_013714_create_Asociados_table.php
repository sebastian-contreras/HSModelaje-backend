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
        Schema::create('Asociados', function (Blueprint $table) {
            $table->unsignedBigInteger('IdAsociado', true);
            $table->unsignedBigInteger('IdContratos')->index('uix_asociados_idcontrato');
            $table->unsignedBigInteger('IdPersona')->index('ref32');
            $table->unsignedInteger('IdCaja');
            $table->char('TipoAsociacion', 1)->nullable();
            $table->char('Relacion', 1)->nullable();
            $table->timestamps();


            $table->index(['IdContratos'], 'idx_contratos');
            $table->index(['IdPersona'], 'idx_persona');
            $table->index(['IdCaja'], 'idx_caja');
            $table->index(['IdContratos', 'IdCaja'], 'ref61');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Asociados');
    }
};
