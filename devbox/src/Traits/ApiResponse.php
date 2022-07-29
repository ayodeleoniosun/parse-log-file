<?php

namespace App\Traits;

use App\Entity\LogAnalytics;
use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponse
{
    public function response(string $status, string $message, LogAnalytics $response, int $statusCode): JsonResponse
    {
        return new JsonResponse([
            'status'   => $status,
            'message'  => $message,
            'response' => $response
        ], $statusCode);
    }
}
