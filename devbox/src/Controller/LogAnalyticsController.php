<?php

namespace App\Controller;

use App\Service\Interfaces\LogAnalyticsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAnalyticsController extends AbstractController
{
    private LogAnalyticsServiceInterface $logService;

    public function __construct(LogAnalyticsServiceInterface $logService)
    {
        $this->logService = $logService;
    }

    public function filter(Request $request): JsonResponse
    {
        $response = $this->logService->filter($request);

        return new JsonResponse([
            'counter' => count($response),
        ], Response::HTTP_OK);
    }
}
