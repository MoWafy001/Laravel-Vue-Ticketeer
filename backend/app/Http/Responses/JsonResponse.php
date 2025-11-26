<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse as HttpJsonResponse;

class JsonResponse
{
    public static function success(string $message = null, mixed $data = null, int $status = 200, array $meta = []): HttpJsonResponse
    {
        $response = [
            'status' => 'success',
            'data' => $data,
            'meta' => array_merge([
                'timestamp' => now()->toIso8601String(),
                'version' => '1.0',
            ], $meta),
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    public static function error(string $message = null, mixed $error = null, int $status = 400, array $meta = []): HttpJsonResponse
    {
        $response = [
            'status' => 'error',
            'error' => $error,
            'data' => null,
            'meta' => array_merge([
                'timestamp' => now()->toIso8601String(),
                'version' => '1.0',
            ], $meta),
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    public static function created(string $message = null, mixed $data = null): HttpJsonResponse
    {
        return self::success($message, $data, 201);
    }
}
