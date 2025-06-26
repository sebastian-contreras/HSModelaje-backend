<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CALL `bsp_alta_usuario`(?, ?, ?, ?, ?,?,?,?)', ['admin', 'contreras', 'sebastian', '1999-06-19', '3813852476', 'admin@admin.com', 'password', 'A']);
        DB::statement('CALL `bsp_alta_usuario`(?, ?, ?, ?, ?,?,?,?)', ['portero', 'portero', 'apellido', '1999-06-19', '3813852476', 'portero@portero.com', 'password', 'G']);
        DB::statement('CALL `bsp_alta_usuario`(?, ?, ?, ?, ?,?,?,?)', ['moderador', 'moderador', 'apellido', '1999-06-19', '3813852476', 'moderador@moderador.com', 'password', 'M']);
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
            DB::statement('CALL `bsp_alta_establecimiento`(?, ?, ?)', $establecimiento);
        }

        // Datos eventos
        $eventos = [
            ['Festival de Luz', '2023-11-01', '2023-11-05', 'S', 1,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Concierto de Estrellas', '2023-11-10', '2023-11-12', 'N', 2,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Cuentos de Otoño', '2023-11-15', '2023-11-20', 'S', 3,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Mercado Mágico', '2023-11-25', '2023-11-30', 'N', 4,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Aventura en el Bosque', '2023-12-01', '2023-12-05', 'S', 5,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Noche de Dragones', '2023-12-10', '2023-12-15', 'N', 6,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Baile de los Elementos', '2023-12-20', '2023-12-25', 'S', 7,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Fiesta de los Sueños', '2023-12-26', '2023-12-30', 'N', 8,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Caminata de los Espíritus', '2024-01-05', '2024-01-10', 'S', 9,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
            ['Reyes de la Noche', '2024-01-15', '2024-01-20', 'N', 10,'Sebastian Contreras','SEBASCON.NARANJA','0001255437652346553465'],
        ];

        foreach ($eventos as $evento) {
            DB::statement('CALL `bsp_alta_evento`(?, ?, ?, ?, ?,?,?,?)', $evento);
        }

        $gastos = [
            [1, 'Alquiler de sala', 'Juan Pérez', 1500.00, 'Factura 001'],
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
            [10, 'Servicios de limpieza', 'Natalia Aguirre', 700.00, 'Factura 020']
        ];

        foreach ($gastos as $gasto) {
            DB::statement('CALL `bsp_alta_gasto`(?, ?, ?, ?, ?)', $gasto);
        }


            DB::table('Patrocinadores')->insert([
            ['IdEvento' => 10, 'Patrocinador' => 'ModaXpress', 'Correo' => 'contacto@modaxpress.com', 'Telefono' => '1155550011', 'DomicilioRef' => 'Av. Corrientes 1234, CABA', 'Descripcion' => 'Tienda de ropa urbana con estilo innovador.', 'FechaCreado' => Carbon::now()],
            ['IdEvento' => 10, 'Patrocinador' => 'Belleza Total', 'Correo' => 'info@bellezatotal.com', 'Telefono' => '1155550012', 'DomicilioRef' => 'Calle Mendoza 432, Rosario', 'Descripcion' => 'Centro estético especializado en eventos.', 'FechaCreado' => Carbon::now()],
            ['IdEvento' => 10, 'Patrocinador' => 'Revista Glam', 'Correo' => 'glam@revistas.com', 'Telefono' => '1155550013', 'DomicilioRef' => 'Diagonal Norte 1000, CABA', 'Descripcion' => 'Revista líder en moda y tendencias.', 'FechaCreado' => Carbon::now()],
            ['IdEvento' => 10, 'Patrocinador' => 'Agua Viva', 'Correo' => 'ventas@aguaviva.com', 'Telefono' => '1155550014', 'DomicilioRef' => 'Ruta 8 km 55, Pilar', 'Descripcion' => 'Distribuidor oficial de agua mineral para eventos.', 'FechaCreado' => Carbon::now()],
            ['IdEvento' => 10, 'Patrocinador' => 'Studio Flash', 'Correo' => 'studio@flashphoto.com', 'Telefono' => '1155550015', 'DomicilioRef' => 'San Martín 300, Córdoba', 'Descripcion' => 'Estudio de fotografía profesional con experiencia en desfiles.', 'FechaCreado' => Carbon::now()],
        ]);
        
        $jueces = [
            [1, '12345678901', 'Juan Pérez', 'juan.perez@example.com', '1234567890'],
            [2, '23456789012', 'María López', 'maria.lopez@example.com', '1234567891'],
            [3, '34567890123', 'Carlos García', 'carlos.garcia@example.com', '1234567892'],
            [4, '45678901234', 'Ana Martínez', 'ana.martinez@example.com', '1234567893'],
            [5, '56789012345', 'Luis Fernández', 'luis.fernandez@example.com', '1234567894'],
            [6, '67890123456', 'Laura Sánchez', 'laura.sanchez@example.com', '1234567895'],
            [7, '78901234567', 'Javier Torres', 'javier.torres@example.com', '1234567896'],
            [8, '89012345678', 'Sofía Ramírez', 'sofia.ramirez@example.com', '1234567897'],
            [9, '90123456789', 'Diego Morales', 'diego.morales@example.com', '1234567898'],
            [10, '01234567890', 'Clara Jiménez', 'clara.jimenez@example.com', '1234567899'],
            [1, '13579246801', 'Fernando Díaz', 'fernando.diaz@example.com', '1234567800'],
            [2, '24681357902', 'Patricia Ruiz', 'patricia.ruiz@example.com', '1234567801'],
            [3, '35792468013', 'Andrés Castro', 'andres.castro@example.com', '1234567802'],
            [4, '46813579204', 'Verónica Ortega', 'veronica.ortega@example.com', '1234567803'],
            [5, '57924680315', 'Ricardo Romero', 'ricardo.romero@example.com', '1234567804'],
            [6, '68035791426', 'Gabriela Herrera', 'gabriela.herrera@example.com', '1234567805'],
            [7, '79146802537', 'Samuel Mendoza', 'samuel.mendoza@example.com', '1234567806'],
            [10, '80257913648', 'Isabel Castro', 'isabel.castro@example.com', '1234567807'],
            [10, '91368024759', 'Hugo Salazar', 'hugo.salazar@example.com', '1234567808'],
            [10, '02479135860', 'Natalia Paredes', 'natalia.paredes@example.com', '1234567809']
        ];


        foreach ($jueces as $juez) {
            DB::statement('CALL `bsp_alta_juez`(?, ?, ?, ?, ?)', $juez);
        }

        $zonas = [
            [1, 'Zona VIP', 50, 'S', 150.00, 'Zona exclusiva con servicio premium.'],
            [2, 'Zona Familiar', 100, 'N', 75.00, 'Zona ideal para familias con niños.'],
            [3, 'Zona General', 200, 'N', 30.00, 'Acceso general para todos los asistentes.'],
            [4, 'Zona de Conciertos', 150, 'S', 120.00, 'Zona con mejor acústica para conciertos.'],
            [5, 'Zona Lounge', 80, 'S', 200.00, 'Zona con asientos cómodos y servicio de bar.'],
            [6, 'Zona de Deportes', 120, 'N', 50.00, 'Zona para disfrutar de eventos deportivos.'],
            [7, 'Zona de Eventos Especiales', 60, 'S', 180.00, 'Zona para eventos privados y especiales.'],
            [8, 'Zona de Exposición', 90, 'N', 40.00, 'Zona para exposiciones y ferias.'],
            [9, 'Zona de Relax', 70, 'S', 160.00, 'Zona tranquila para relajarse.'],
            [10, 'Zona de Comida', 150, 'N', 25.00, 'Zona con variedad de opciones gastronómicas.'],
            [1, 'Zona de Juegos', 200, 'N', 20.00, 'Zona con juegos y entretenimiento para niños.'],
            [2, 'Zona de Arte', 50, 'S', 100.00, 'Zona dedicada a exposiciones de arte.'],
            [3, 'Zona de Networking', 80, 'N', 90.00, 'Zona para hacer contactos y networking.'],
            [4, 'Zona de Tecnología', 120, 'S', 110.00, 'Zona con demostraciones de tecnología.'],
            [5, 'Zona de Cine', 150, 'N', 70.00, 'Zona para proyecciones de películas.'],
            [6, 'Zona de Música', 100, 'S', 130.00, 'Zona para disfrutar de música en vivo.'],
            [7, 'Zona de Fitness', 80, 'N', 60.00, 'Zona para actividades deportivas y fitness.'],
            [8, 'Zona de Bienestar', 70, 'S', 140.00, 'Zona dedicada al bienestar y la salud.'],
            [9, 'Zona de Innovación', 90, 'N', 95.00, 'Zona para presentar ideas innovadoras.'],
            [10, 'Zona de Celebraciones', 150, 'S', 200.00, 'Zona para celebraciones y eventos especiales.']
        ];

        foreach ($zonas as $zona) {
            DB::statement('CALL `bsp_alta_zona`(?, ?, ?, ?, ?,?)', $zona);
        }


        $metricas = [
            [1, 'Capacidad de pose'],
            [1, 'Variedad de looks'],
            [1, 'Estilo personal'],
            [1, 'Confianza en pasarela'],
            [1, 'Habilidad para combinar prendas'],
            [1, 'Expresión facial'],
            [1, 'Actitud profesional'],
            [1, 'Conocimiento de tendencias'],
            [1, 'Capacidad de improvisación'],
            [1, 'Apariencia general']
        ];
        foreach ($metricas as $metrica) {
            DB::statement('CALL `bsp_alta_metrica`(?, ?)', $metrica);
        }



        $entradas =

            [
                [1, 'Apellido1', '12345678', 'correo1@example.com', '1111111111', 'comprobante1', 1],
                [2, 'Apellido2', '23456789', 'correo2@example.com', '2222222222', 'comprobante2', 1],
                [3, 'Apellido3', '34567890', 'correo3@example.com', '3333333333', 'comprobante3', 1],
                [4, 'Apellido4', '45678901', 'correo4@example.com', '4444444444', 'comprobante4', 1],
                [5, 'Apellido5', '56789012', 'correo5@example.com', '5555555555', 'comprobante5', 1],
                [6, 'Apellido6', '67890123', 'correo6@example.com', '6666666666', 'comprobante6', 1],
                [7, 'Apellido7', '78901234', 'correo7@example.com', '7777777777', 'comprobante7', 1],
                [8, 'Apellido8', '89012345', 'correo8@example.com', '8888888888', 'comprobante8', 1],
                [9, 'Apellido9', '90123456', 'correo9@example.com', '9999999999', 'comprobante9', 1],
                [10, 'Apellido10', '01234567', 'correo10@example.com', '1010101010', 'comprobante10', 1],
                [11, 'Apellido11', '11234567', 'correo11@example.com', '1111111111', 'comprobante11', 1],
                [12, 'Apellido12', '12234567', 'correo12@example.com', '1212121212', 'comprobante12', 1],
                [13, 'Apellido13', '13234567', 'correo13@example.com', '1313131313', 'comprobante13', 1],
                [14, 'Apellido14', '14234567', 'correo14@example.com', '1414141414', 'comprobante14', 1],
                [15, 'Apellido15', '15234567', 'correo15@example.com', '1515151515', 'comprobante15', 1],
                [16, 'Apellido16', '16234567', 'correo16@example.com', '1616161616', 'comprobante16', 1],
                [17, 'Apellido17', '17234567', 'correo17@example.com', '1717171717', 'comprobante17', 1],
                [18, 'Apellido18', '18234567', 'correo18@example.com', '1818181818', 'comprobante18', 1],
                [19, 'Apellido19', '19234567', 'correo19@example.com', '1919191919', 'comprobante19', 1],
                [20, 'Apellido20', '20234567', 'correo20@example.com', '2020202020', 'comprobante20', 1],
                [1, 'González Martín', '30123456', 'martin.gonzalez@example.com', '1134567890', 'comp_001', 1],
                [2, 'Rodríguez Ana', '31234567', 'ana.rodriguez@example.com', '1145678901', 'comp_002', 1],
                [3, 'Fernández Pablo', '32345678', 'pablo.fernandez@example.com', '1156789012', 'comp_003', 1],
                [4, 'López María', '33456789', 'maria.lopez@example.com', '1167890123', 'comp_004', 1],
                [5, 'Martínez Javier', '34567890', 'javier.martinez@example.com', '1178901234', 'comp_005', 1],
                [6, 'Gómez Carolina', '35678901', 'carolina.gomez@example.com', '1189012345', 'comp_006', 1],
                [7, 'Díaz Lucas', '36789012', 'lucas.diaz@example.com', '1190123456', 'comp_007', 1],
                [8, 'Pérez Valentina', '37890123', 'valentina.perez@example.com', '1201234567', 'comp_008', 1],
                [9, 'Sánchez Mateo', '38901234', 'mateo.sanchez@example.com', '1212345678', 'comp_009', 1],
                [10, 'Ramírez Camila', '39012345', 'camila.ramirez@example.com', '1223456789', 'comp_010', 1],
                [11, 'Torres Nicolás', '40123456', 'nicolas.torres@example.com', '1234567890', 'comp_011', 1],
                [12, 'Flores Sofía', '41234567', 'sofia.flores@example.com', '1245678901', 'comp_012', 1],
                [20, 'Álvarez Juan', '42345678', 'juan.alvarez@example.com', '1256789012', 'comp_013', 1],
                [10, 'Morales Agustina', '43456789', 'agustina.morales@example.com', '1267890123', 'comp_014', 1],
                [10, 'Castro Bruno', '44567890', 'bruno.castro@example.com', '1278901234', 'comp_015', 1],
                [10, 'Ortiz Emilia', '45678901', 'emilia.ortiz@example.com', '1289012345', 'comp_016', 1],
                [10, 'Méndez Santiago', '46789012', 'santiago.mendez@example.com', '1290123456', 'comp_017', 1],
                [20, 'Vargas Lucía', '47890123', 'lucia.vargas@example.com', '1301234567', 'comp_018', 1],
                [20, 'Rojas Facundo', '48901234', 'facundo.rojas@example.com', '1312345678', 'comp_019', 1],
                [20, 'Navarro Julieta', '49012345', 'julieta.navarro@example.com', '1323456789', 'comp_020', 1]
            ]
        ;
        foreach ($entradas as $entrada) {
            DB::statement('CALL `bsp_alta_entrada`(?, ?,?,?,?,?,?)', $entrada);
        }

        DB::table('Modelos')->insert([
            ['DNI' => '10000001', 'ApelName' => 'Ana Pérez', 'FechaNacimiento' => '1998-04-12', 'Sexo' => 'F', 'Telefono' => '1155550001', 'Correo' => 'ana.perez@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000002', 'ApelName' => 'Juan López', 'FechaNacimiento' => '1995-09-22', 'Sexo' => 'M', 'Telefono' => '1155550002', 'Correo' => 'juan.lopez@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000003', 'ApelName' => 'Lucía García', 'FechaNacimiento' => '2000-06-10', 'Sexo' => 'F', 'Telefono' => '1155550003', 'Correo' => 'lucia.garcia@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000004', 'ApelName' => 'Carlos Díaz', 'FechaNacimiento' => '1999-02-01', 'Sexo' => 'M', 'Telefono' => '1155550004', 'Correo' => 'carlos.diaz@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000005', 'ApelName' => 'Valentina Ruiz', 'FechaNacimiento' => '2001-07-18', 'Sexo' => 'F', 'Telefono' => '1155550005', 'Correo' => 'valentina.ruiz@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000006', 'ApelName' => 'Marcos Gómez', 'FechaNacimiento' => '1997-11-09', 'Sexo' => 'M', 'Telefono' => '1155550006', 'Correo' => 'marcos.gomez@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000007', 'ApelName' => 'Julieta Castro', 'FechaNacimiento' => '2000-01-21', 'Sexo' => 'F', 'Telefono' => '1155550007', 'Correo' => 'julieta.castro@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000008', 'ApelName' => 'Martín Rivas', 'FechaNacimiento' => '1996-05-30', 'Sexo' => 'M', 'Telefono' => '1155550008', 'Correo' => 'martin.rivas@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000009', 'ApelName' => 'Sofía Blanco', 'FechaNacimiento' => '1998-08-25', 'Sexo' => 'F', 'Telefono' => '1155550009', 'Correo' => 'sofia.blanco@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000010', 'ApelName' => 'Diego Herrera', 'FechaNacimiento' => '2002-03-17', 'Sexo' => 'M', 'Telefono' => '1155550010', 'Correo' => 'diego.herrera@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000011', 'ApelName' => 'Camila Torres', 'FechaNacimiento' => '1999-10-03', 'Sexo' => 'F', 'Telefono' => '1155550011', 'Correo' => 'camila.torres@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000012', 'ApelName' => 'Nicolás Vega', 'FechaNacimiento' => '1997-12-12', 'Sexo' => 'M', 'Telefono' => '1155550012', 'Correo' => 'nicolas.vega@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000013', 'ApelName' => 'Agustina Luna', 'FechaNacimiento' => '2000-04-05', 'Sexo' => 'F', 'Telefono' => '1155550013', 'Correo' => 'agustina.luna@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000014', 'ApelName' => 'Tomás Moreno', 'FechaNacimiento' => '1996-06-14', 'Sexo' => 'M', 'Telefono' => '1155550014', 'Correo' => 'tomas.moreno@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000015', 'ApelName' => 'Milagros Cabrera', 'FechaNacimiento' => '2001-02-27', 'Sexo' => 'F', 'Telefono' => '1155550015', 'Correo' => 'milagros.cabrera@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000016', 'ApelName' => 'Lucas Navarro', 'FechaNacimiento' => '1995-09-19', 'Sexo' => 'M', 'Telefono' => '1155550016', 'Correo' => 'lucas.navarro@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000017', 'ApelName' => 'Martina Peña', 'FechaNacimiento' => '2003-01-11', 'Sexo' => 'F', 'Telefono' => '1155550017', 'Correo' => 'martina.pena@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000018', 'ApelName' => 'Facundo Silva', 'FechaNacimiento' => '1998-07-20', 'Sexo' => 'M', 'Telefono' => '1155550018', 'Correo' => 'facundo.silva@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000019', 'ApelName' => 'Brenda Salas', 'FechaNacimiento' => '2002-12-06', 'Sexo' => 'F', 'Telefono' => '1155550019', 'Correo' => 'brenda.salas@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000020', 'ApelName' => 'Gabriel Fuentes', 'FechaNacimiento' => '1996-11-23', 'Sexo' => 'M', 'Telefono' => '1155550020', 'Correo' => 'gabriel.fuentes@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000021', 'ApelName' => 'Emilia Bravo', 'FechaNacimiento' => '2000-10-08', 'Sexo' => 'F', 'Telefono' => '1155550021', 'Correo' => 'emilia.bravo@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000022', 'ApelName' => 'Julián Romero', 'FechaNacimiento' => '1997-03-04', 'Sexo' => 'M', 'Telefono' => '1155550022', 'Correo' => 'julian.romero@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000023', 'ApelName' => 'Melina Ferreira', 'FechaNacimiento' => '2001-08-15', 'Sexo' => 'F', 'Telefono' => '1155550023', 'Correo' => 'melina.ferreira@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000024', 'ApelName' => 'Franco Aguirre', 'FechaNacimiento' => '1999-05-01', 'Sexo' => 'M', 'Telefono' => '1155550024', 'Correo' => 'franco.aguirre@example.com', 'EstadoMod' => 'A'],
            ['DNI' => '10000025', 'ApelName' => 'Rocío Méndez', 'FechaNacimiento' => '1998-01-31', 'Sexo' => 'F', 'Telefono' => '1155550025', 'Correo' => 'rocio.mendez@example.com', 'EstadoMod' => 'A'],
        ]);

        DB::table('Metricas')->insert([
            ['IdEvento' => 10, 'Metrica' => 'Porte y elegancia en pasarela', 'EstadoMetrica' => 'A'],
            ['IdEvento' => 10, 'Metrica' => 'Vestimenta y presentación', 'EstadoMetrica' => 'A'],
            ['IdEvento' => 10, 'Metrica' => 'Actitud y seguridad', 'EstadoMetrica' => 'A'],
            ['IdEvento' => 10, 'Metrica' => 'Originalidad del desfile', 'EstadoMetrica' => 'A'],
            ['IdEvento' => 10, 'Metrica' => 'Interacción con el público', 'EstadoMetrica' => 'A'],
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
