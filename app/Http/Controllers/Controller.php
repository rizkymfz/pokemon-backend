<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function success($data = null, $message = null, $status = 'success')
    {
        $responses = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($responses, Response::HTTP_OK);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message)
    {
        $responses = [
            'status' => "error",
            'message' => $message
        ];

        return response()->json($responses, Response::HTTP_BAD_REQUEST);
    }
}
