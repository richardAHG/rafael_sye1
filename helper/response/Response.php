<?php

class Response
{
    public static function JSON($status, $mensaje, $data = [], $acceso = false)
    {
        $response = [];
        $response = [
            'status' => $status,
            'mensaje' => $mensaje,
            'data' => $data,
            'acceso' => $acceso
        ];

        echo json_encode($response);
    }
}
