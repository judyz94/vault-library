<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    protected function success($data, string $message = '', int $status = 200): JsonResponse
    {
        if ($data instanceof JsonResource) {
            return $data->additional([
                'status' => 'success',
                'message' => $message,
            ])->response()->setStatusCode($status);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(string $message, int $status = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}

