<?php

namespace App\Http\Controllers;

use App\Classes\Eventos;
use App\Helpers\ResponseFormatter;
use App\Services\GestorEventos;
use App\Services\GestorInformes;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;

class InformesController extends Controller
{
    //
    protected $gestorInformes;

    public function __construct(GestorInformes $gestorInformes, GestorEventos $gestorEventos)
    {
        $this->gestorInformes = $gestorInformes;
        $this->gestorEventos = $gestorEventos;
    }


    public function informeVotacion(int $pIdEvento)
    {
        $IdEvento = intval($pIdEvento); // Valor por defecto 'N'

        try {

            $evento = new Eventos(['IdEvento' => $IdEvento]);
            $rawResults = $evento->ListarVotos();

            $models = [];

            foreach ($rawResults as $row) {
                $IdParticipante = $row->IdParticipante;
                $idJuez = $row->IdJuez;

                // Inicializar modelo si no existe
                if (!isset($models[$IdParticipante])) {
                    $models[$IdParticipante] = [
                        'IdParticipante' => $IdParticipante,
                        'DNIModelo' => $row->DNIModelo,
                        'Apelname' => $row->ApelNameModelo,
                        'votes' => [],
                        'totalScore' => 0,
                        'metricCount' => 0,
                        'judgeSet' => [], // para contar votos únicos
                    ];
                }

                // Clave para agrupar por juez
                $judgeKey = "judge_$idJuez";
                if (!isset($models[$IdParticipante]['votes'][$judgeKey])) {
                    $models[$IdParticipante]['votes'][$judgeKey] = [
                        'IdJuez' => $idJuez,
                        'judgeName' => $row->ApelNameJuez,
                        'metrics' => [],
                    ];
                    $models[$IdParticipante]['judgeSet'][$idJuez] = true; // registrar juez único
                }

                // Agregar métrica
                $models[$IdParticipante]['votes'][$judgeKey]['metrics'][] = [
                    'name' => $row->Metrica,
                    'score' => floatval($row->Nota),
                    'maxScore' => 10 // asumido
                ];

                // Acumular nota
                $models[$IdParticipante]['totalScore'] += floatval($row->Nota);
                $models[$IdParticipante]['metricCount']++;
            }

            // Procesar salida final
            $output = [];
            foreach ($models as $model) {
                $votesArray = array_values($model['votes']);
                $average = $model['metricCount'] > 0
                    ? $model['totalScore'] / $model['metricCount']
                    : 0;

                $output[] = [
                    'IdParticipante' => $model['IdParticipante'],
                    'DNIModelo' => $model['DNIModelo'],
                    'Apelname' => $model['Apelname'],
                    'averageScore' => round($average, 2),
                    'totalVotes' => count($model['judgeSet']),
                    'totalMetrics' => $model['metricCount'],
                    'votes' => $votesArray
                ];
            }



            $imagePath = public_path('logos-web/logo-color-completo.png');
            $image = "data:image/png;base64," . base64_encode(file_get_contents($imagePath));

            $viewData = [
                'image' => $image,
                'data' => $output,
                'titulo' => 'Votacion de ' . $evento->Evento,
            ];

            $pdf = Pdf::loadView('InformeTest', $viewData);
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true
            ]);

            $filename = 'informe_votacion_' . now()->format('Ymd_His') . '.pdf';

            return ResponseFormatter::success([
                'filename' => $filename,
                "pdf" => base64_encode($pdf->output()),
            ]);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }


    public function informeEvento(int $pIdEvento)
    {
        $evento = new Eventos(['IdEvento' => $pIdEvento]);
        $resultadosZona = $evento->InformeZona();
        $resultadosEstado = $evento->InformeEstado();
        $resultadosGastos = $evento->InformeGastos();
        $resultadosPatrocinadores = $evento->InformePatrocinadores();

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
    public function dashboard(int $pIdEvento)
    {
        $evento = new Eventos(['IdEvento' => $pIdEvento]);
        // Ejemplo: obtén varios conjuntos de resultados
        $resultadosZona = $evento->InformeZona();
        $resultadosEstado = $evento->InformeEstado();
        $resultadosGastos = $evento->InformeGastos();

        return ResponseFormatter::success([
            'zona' => $resultadosZona,
            'estado' => $resultadosEstado,
            'gastos' => $resultadosGastos
        ]);
    }
}
