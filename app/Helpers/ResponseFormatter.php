<?php

namespace App\Helpers;

class ResponseFormatter
{
    public static function success($data, $message = 'OK',$code=200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error($message = 'Ocurrio un error, comuniquese con el administrador', $code = 500)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}