<?php

namespace App\Controller;

use App\Service\Interfaces\LogAnalyticsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function count(Request $request): Response
    {
        $response = $this->logService->count($request);

        return $this->response(
            'success',
            'Log analytics retrieved',
            $response,
            Response::HTTP_OK
        );
    }
}
