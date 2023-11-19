<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class BaseResponse
{
    protected static $response = [
        'status'  => 'success',
        'message' => "",
        'data'    => null,
        'meta'    => null
    ];

    public static function success($data = null, $message = "", $paginatedResource = false)
    {
        self::$response['data']    = $data;
        self::$response['message'] = $message;

        if ($data instanceof ResourceCollection && $paginatedResource == true) {
            $meta['current_page'] = $data->currentPage();
            $meta['per_page']     = $data->perPage();
            $meta['total_page']   = $data->lastPage();
            $meta['total_data']   = $data->total();

            $data = collect($data->items());
            self::$response['meta']['pagination'] = $meta;
            self::$response['data'] = $data;
        }

        if ($data instanceof Paginator || $data instanceof LengthAwarePaginator) {
            $meta['current_page'] = $data->toArray()['current_page'];
            $meta['per_page']     = $data->toArray()['per_page'];
            $meta['total_page']   = $data->toArray()['last_page'];
            $meta['total_data']   = $data->toArray()['total'];

            $data = collect($data->items());
            self::$response['meta']['pagination'] = $meta;
            self::$response['data'] = $data;
        }

        return response()->json(self::$response, Response::HTTP_OK);
    }

    public static function error($data = null, $message = "", $code = 400, $error = null)
    {
        self::$response['status']  = 'error';
        self::$response['message'] = $message;
        if ($error != null) {
            self::$response['errors'] = $error;
        }
        self::$response['data'] = $data;

        return response()->json(self::$response, $code);
    }
}
