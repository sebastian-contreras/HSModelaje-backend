<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use DB;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener el parámetro 'pIncluyeBajas' de la solicitud, si es necesario
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N'); // Valor por defecto 'N'

        try {
            // Llamar al procedimiento almacenado
            $usuarios = DB::select('CALL bsp_listar_usuarios(?)', [$pIncluyeBajas]);

            // Devolver el resultado como JSON
            return ResponseFormatter::success($usuarios);
        } catch (\Exception $e) {
            // Manejo de errores
            return ResponseFormatter::error('error al obtener los usuarios.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
        $request->validated();
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_alta_usuario(?, ?, ?, ?, ?, ?, ?, ?)', [
            $request->Username,
            $request->Apellidos,
            $request->Nombres,
            $request->FechaNacimiento,
            $request->Telefono,
            $request->Email,
            $request->Contrasena,
            $request->Rol,
        ]);


        return ResponseFormatter::success($result, 'Usuario creado exitosamente.', 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $IdUsuario)
    {
        //
        $request->validated();
        $result = DB::select('CALL bsp_modifica_perfil(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $request->IdUsuario,
            $request->Username,
            $request->Apellidos,
            $request->Nombres,
            $request->FechaNacimiento,
            $request->Telefono,
            $request->Email,
            $request->Contrasena,
            $request->Rol,
        ]);
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Usuario modificado exitosamente.', 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $IdUsuario)
    {
        // Llamar al procedimiento almacenado
        $result = DB::select('CALL bsp_borra_usuario(?)', [$IdUsuario]);

        // Verificar la respuesta del procedimiento almacenado
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            // Si hay un error, devolver un error formateado
            return ResponseFormatter::error('Error al borrar el usuario.', 400);
        }

        // Si todo fue exitoso, devolver una respuesta de éxito
        return ResponseFormatter::success(null, 'Usuario borrado exitosamente.', 200);
    }
}
