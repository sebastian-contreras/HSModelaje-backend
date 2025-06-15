<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;

class InformesController extends Controller
{
    //
    public function informeEvento(int $pIdEvento)
    {
        // Ejemplo: obtén varios conjuntos de resultados
        $resultadosZona = DB::select("CALL bsp_informe_evento_zona(?)", [$pIdEvento]);
        $resultadosEstado = DB::select("CALL bsp_informe_evento_estado(?)", [$pIdEvento]); // Ejemplo
        $resultadosGastos = DB::select("CALL bsp_informe_evento_gastos(?)", [$pIdEvento]); // Ejemplo
        $resultadosPatrocinadores = DB::select("CALL bsp_informe_evento_patrocinadores(?)", [$pIdEvento]); // Ejemplo 

        $tablas = [
            [
                'titulo' => 'Resumen de venta de entradas por zona',
                'cabecera' => [
                    'Zona' => 'Zona',
                    'Capacidad' => 'Capacidad',
                    'Precio' => 'Precio',
                    'GananciaEsperada' => 'Ganancia Esperada',
                    'Vendidas' => 'Entradas Vendidas',
                    'GananciaReal' => 'Ganancia Real',
                ],
                'data' => $resultadosZona,
            ],
            [
                'titulo' => 'Resumen de venta de entradas por estado',
                'cabecera' => [
                    'EstadoEvento' => 'Estado',
                    'Cantidad' => 'Cantidad',
                    'Importe' => 'Importe',
                ],
                'data' => $resultadosEstado,
            ],
            [
                'titulo' => 'Resumen de gastos',
                'cabecera' => [
                    'Gasto' => 'Gasto',
                    'Personal' => 'Personal',
                    'Monto' => 'Monto',
                    'Comprobante' => 'Comprobante',
                    'FechaCreado' => 'FechaCreado'
                ],
                'data' => $resultadosGastos,
            ],
            [
                'titulo' => 'Patrocinadores registrados',
                'cabecera' => [
                    'Patrocinador' => 'Patrocinador',
                    'Correo' => 'Correo',
                    'Telefono' => 'Telefono',
                    'DomicilioRef' => 'Domicilio',
                    'Descripcion' => 'Descripcion',
                    'FechaCreado' => 'Fecha',
                ],
                'data' => $resultadosPatrocinadores,
            ],
        ];

        $imagePath = public_path('logos-web/logo-color-completo.png');
        $image = "data:image/png;base64," . base64_encode(file_get_contents($imagePath));

        $viewData = [
            'image' => $image,
            'tablas' => $tablas,
            'titulo' => 'Informe de Evento',
        ];

        $pdf = Pdf::loadView('InformeView', $viewData);
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true
        ]);

        $filename = 'informe_evento_' . now()->format('Ymd_His') . '.pdf';

        return ResponseFormatter::success([
            'filename' => $filename,
            "pdf" => base64_encode($pdf->output()),
        ]);
    }
}
