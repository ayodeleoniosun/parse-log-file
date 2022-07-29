<?php

namespace App\Controller;

use App\Service\Interfaces\LogAnalyticsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;

class LogAnalyticsController extends AbstractController
{
    use ApiResponse;

    private LogAnalyticsServiceInterface $logService;

    public function __construct(LogAnalyticsServiceInterface $logService)
    {
        $this->logService = $logService;
    }

    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        $response = $this->logService->store($data);

        return $this->response(
            'success',
            'Log analytics successfully saved',
            $response,
            Response::HTTP_CREATED
        );
    }

    public function count(): Response
    {
        return $this->render('log_analytics/index.html.twig', [
            'controller_name' => 'LogAnalyticsController',
        ]);
    }
}
