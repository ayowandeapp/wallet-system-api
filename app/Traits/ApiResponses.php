<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponses
{

    protected function ok($data)
    {
        return $this->success($data->toArray(), Response::HTTP_OK);
    }

    // protected function client_error($data)
    // {
    //     return $this->error($data->toArray(), 400);
    // }

    protected function error($data, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'message' => $data,
            'status' => $statusCode
        ], $statusCode);
    }
    protected function success($data, int $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'data' => $data,
            'status' => $statusCode
        ], $statusCode);
    }
}
