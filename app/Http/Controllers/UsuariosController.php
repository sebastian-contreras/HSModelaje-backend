<?php
namespace App\Http\Controllers;

use App\Classes\Usuarios;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\GestorUsuarios; // Importa el servicio
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    protected $gestorUsuarios;

    public function __construct(GestorUsuarios $gestorUsuarios)
    {
        $this->gestorUsuarios = $gestorUsuarios;
    }

    public function index(Request $request)
    {
        $pIncluyeBajas = $request->input('pIncluyeBajas', 'N');
        try {
            $usuarios = $this->gestorUsuarios->Listar($pIncluyeBajas);
            return ResponseFormatter::success($usuarios);
        } catch (\Exception $e) {
            return ResponseFormatter::error('error al obtener los usuarios.', 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        $request->validated();
        $usuario = new Usuarios($request->all());
        $result = $this->gestorUsuarios->Alta($usuario);

        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Usuario creado exitosamente.', 201);
    }

    public function update(UpdateUserRequest $request, int $IdUsuario)
    {
        $request->validated();
        $usuario = new Usuarios($request->all());
        $result = $this->gestorUsuarios->Modifica($usuario);
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success($result, 'Usuario modificado exitosamente.', 201);
    }

    public function destroy(int $IdUsuario)
    {
        $result = $this->gestorUsuarios->Borra($IdUsuario);
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error('Error al borrar el usuario.', 400);
        }
        return ResponseFormatter::success(null, 'Usuario borrado exitosamente.', 200);
    }
    public function darBaja(int $IdUsuario)
    {
        $usuario = new Usuarios(['IdUsuario' => $IdUsuario]);
        $result = $usuario->DarBaja();
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success(null, 'Usuario dado de baja exitosamente.', 200);
    }

    public function activar(int $IdUsuario)
    {
        $usuario = new Usuarios(['IdUsuario' => $IdUsuario]);
        $result = $usuario->Activar();
        if (isset($result[0]->Response) && $result[0]->Response === 'error') {
            return ResponseFormatter::error($result[0]->Mensaje, 400);
        }
        return ResponseFormatter::success(null, 'Usuario activo exitosamente.', 200);
    }
}