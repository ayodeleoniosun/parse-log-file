<?php

namespace App\Service\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface LogAnalyticsServiceInterface
{
    public function store(object $data): void;

    public function filter(Request $request): array;
}
