<?php

namespace App\Http\Controllers;

use App\Events\ListadoVotosParticipantes;
use App\Events\VotoModeloIniciado;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\VotosRequest;
use DB;
use Illuminate\Http\Request;
use Log;

class VotacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listar(Request $request)
    {
        $pIdEvento = intval($request->input('pIdEvento')); // Valor por defecto 'N'

        try {
            $rawResults = DB::select('CALL bsp_listar_votos(?)', [$pIdEvento]);

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


            return ResponseFormatter::success(response()->json($output)->original);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los votos.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function alta(VotosRequest $request)
    {
        $data = $request->validated();

        $IdParticipante = $request->IdParticipante;
        $IdJuez = $request->IdJuez;
        $IdEvento = $request->IdEvento;
        $votos = $request->votos;

        $errores = [];
        foreach ($votos as $voto) {
            $result = DB::select('CALL bsp_alta_voto(?, ?, ?, ?, ?)', [
                $IdParticipante,
                $IdJuez,
                $voto['IdMetrica'],
                $voto['Nota'],
                null
            ]);

            if (isset($result[0]->Response) && $result[0]->Response === 'error') {
                $errores[] = $result[0]->Mensaje;
            }
        }

        if (!empty($errores)) {
            return ResponseFormatter::error($errores, 400);
        }

        $listado = $this->listar(new Request(['pIdEvento' => $IdEvento]))->getData()->data;
        broadcast(new ListadoVotosParticipantes($listado, $IdEvento));

        return ResponseFormatter::success(null, 'Votos asignados exitosamente.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function iniciarVotacionParticipante(Request $request)
    {
        //

        $IdParticipante = $request->input('pIdParticipante');
        $result = DB::select('CALL bsp_dame_participante(?)', [$IdParticipante]);
        try {
            DB::select('CALL bsp_iniciar_votacion_participante(?)', [$IdParticipante]);

            broadcast(new VotoModeloIniciado($result[0], 'iniciar'));
            return ResponseFormatter::success($result[0]);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 200);
        }
    }

    public function detenerVotacionParticipante(Request $request)
    {
        //

        $IdParticipante = $request->input('pIdParticipante');
        $result = DB::select('CALL bsp_dame_participante(?)', [$IdParticipante]);
        try {
            DB::select('CALL bsp_detener_votacion_participante(?)', [$IdParticipante]);
            broadcast(new VotoModeloIniciado($result[0], 'detener'));
            return ResponseFormatter::success($result[0]);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 200);
        }
    }

    public function reiniciarVoto(Request $request)
    {
        $IdParticipante = $request->input('pIdParticipante');
        $IdEvento = $request->input('pIdEvento');

        try {

            $result = DB::select('CALL bsp_reiniciar_votacion_participante(?)', [$IdParticipante]);

            // Verificar la respuesta del procedimiento almacenado
            if (isset($result[0]->Response) && $result[0]->Response === 'error') {
                // Si hay un error, devolver un error formateado
                return ResponseFormatter::error('Error al borrar al reiniciar la votacion.', 400);
            }

            $listado = $this->listar(new Request(['pIdEvento' => $IdEvento]))->getData()->data;
            broadcast(new ListadoVotosParticipantes($listado, $IdEvento));


            // Si todo fue exitoso, devolver una respuesta de éxito
            return ResponseFormatter::success(null, 'Se reinicio correctamente la votacion del modelo.', 200);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 200);
        }
    }
    public function ActivoVotacionParticipante(Request $request)
    {
        //

        $IdEvento = $request->input('pIdEvento');
        try {
            $result = DB::select('CALL bsp_dame_votacion_participante(?)', [$IdEvento]);
            return ResponseFormatter::success($result);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }



    public function iniciarVotacion(Request $request)
    {
        //

        $IdEvento = $request->input('pIdEvento');
        $result = DB::select('CALL bsp_iniciar_votacion(?)', [$IdEvento]);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result[0]);
    }




    public function finalizarVotacion(Request $request)
    {
        //

        $IdEvento = $request->input('pIdEvento');
        $result = DB::select('CALL bsp_finalizar_votacion(?)', [$IdEvento]);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result[0]);
    }

}
