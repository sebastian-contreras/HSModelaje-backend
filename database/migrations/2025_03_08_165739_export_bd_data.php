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
        // Establecimientos
        $establecimientos = [
            ['Café Tortoni', 'Buenos Aires', 100],
            ['La Boca', 'Buenos Aires', 200],
            ['El Club de la Milanesa', 'Buenos Aires', 150],
            ['Don Julio', 'Buenos Aires', 80],
            ['Pizzería Güerrin', 'Buenos Aires', 120],
            ['Cabaña Las Lilas', 'Buenos Aires', 200],
            ['El Palacio de la Papa Frita', 'Buenos Aires', 300],
            ['Sushi Club', 'Buenos Aires', 150],
            ['La Parolaccia', 'Buenos Aires', 100],
            ['Freddo', 'Buenos Aires', 50],
            ['Café de los Angelitos', 'Buenos Aires', 120],
            ['El Ferroviario', 'Buenos Aires', 200],
            ['Los Inmortales', 'Buenos Aires', 180],
            ['La Bomba de Tiempo', 'Buenos Aires', 250],
            ['Café Martínez', 'Buenos Aires', 90],
            ['Tierra de Fuego', 'Buenos Aires', 300],
            ['El Sanjuanino', 'Buenos Aires', 150],
            ['Café de la Plaza', 'Buenos Aires', 70],
            ['La Cabaña', 'Buenos Aires', 200],
            ['El Rincón de los Abuelos', 'Buenos Aires', 120],
        ];

        foreach ($establecimientos as $establecimiento) {
            DB::statement('CALL `HSModelaje_db`.`bsp_alta_establecimientos`(?, ?, ?)', $establecimiento);
        }

        // Datos eventos
        $eventos = [
            ['Festival de Luz', '2023-11-01', '2023-11-05', 'S', 1],
            ['Concierto de Estrellas', '2023-11-10', '2023-11-12', 'N', 2],
            ['Cuentos de Otoño', '2023-11-15', '2023-11-20', 'S', 3],
            ['Mercado Mágico', '2023-11-25', '2023-11-30', 'N', 4],
            ['Aventura en el Bosque', '2023-12-01', '2023-12-05', 'S', 5],
            ['Noche de Dragones', '2023-12-10', '2023-12-15', 'N', 6],
            ['Baile de los Elementos', '2023-12-20', '2023-12-25', 'S', 7],
            ['Fiesta de los Sueños', '2023-12-26', '2023-12-30', 'N', 8],
            ['Caminata de los Espíritus', '2024-01-05', '2024-01-10', 'S', 9],
            ['Reyes de la Noche', '2024-01-15', '2024-01-20', 'N', 10],
        ];

        foreach ($eventos as $evento) {
            DB::statement('CALL `HSModelaje_db`.`bsp_alta_evento`(?, ?, ?, ?, ?)', $evento);
        }

        $gastos = [[1, 'Alquiler de sala', 'Juan Pérez', 1500.00, 'Factura 001'],
        [2, 'Catering', 'María López', 2500.50, 'Factura 002'],
        [3, 'Material promocional', 'Carlos García', 800.75, 'Factura 003'],
        [4, 'Transporte', 'Ana Martínez', 1200.00, 'Factura 004'],
        [5, 'Publicidad', 'Luis Fernández', 3000.00, 'Factura 005'],
        [6, 'Decoración', 'Sofía Rodríguez', 950.25, 'Factura 006'],
        [7, 'Sonido y luces', 'Diego Torres', 1800.00, 'Factura 007'],
        [8, 'Fotografía', 'Laura Sánchez', 2200.50, 'Factura 008'],
        [9, 'Seguridad', 'Javier Morales', 1300.00, 'Factura 009'],
        [10, 'Impresiones', 'Claudia Jiménez', 600.00, 'Factura 010'],
        [1, 'Alquiler de equipo', 'Fernando Ruiz', 1750.00, 'Factura 011'],
        [2, 'Regalos promocionales', 'Patricia Díaz', 400.00, 'Factura 012'],
        [3, 'Tarta de cumpleaños', 'Ricardo Castro', 500.00, 'Factura 013'],
        [4, 'Música en vivo', 'Verónica Herrera', 2500.00, 'Factura 014'],
        [5, 'Alquiler de sillas', 'Gabriel Romero', 300.00, 'Factura 015'],
        [6, 'Video promocional', 'Isabel Ortega', 1200.00, 'Factura 016'],
        [7, 'Técnico de sonido', 'Andrés Salazar', 800.00, 'Factura 017'],
        [8, 'Transporte de invitados', 'Mónica Ríos', 1500.00, 'Factura 018'],
        [9, 'Alquiler de carpa', 'Hugo Mendoza', 2000.00, 'Factura 019'],
        [10, 'Servicios de limpieza', 'Natalia Aguirre', 700.00, 'Factura 020']];

        foreach ($gastos as $gasto) {
            DB::statement('CALL `HSModelaje_db`.`bsp_alta_gasto`(?, ?, ?, ?, ?)', $gasto);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
