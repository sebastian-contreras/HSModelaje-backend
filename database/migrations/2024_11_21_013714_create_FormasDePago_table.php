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
        Schema::create('FormasDePago', function (Blueprint $table) {
            $table->unsignedBigInteger('IdFormaDePago', true);
            $table->string('TipoFormaPago', 30)->nullable();
            $table->string('MarcaTarjeta', 40)->nullable();
            $table->string('BancoEmisor', 100)->nullable();
            $table->string('Titular', 150)->nullable();
            $table->string('NumeroTarjeta', 50)->nullable();
            $table->date('FVencimiento')->nullable();
            $table->char('CodSeguridad', 10)->nullable();
            $table->string('CBU', 50)->nullable();
            $table->char('PagoAdelantado', 1)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('FormasDePago');
    }
};
