<?php

namespace App\Repository\Interfaces;

use App\Entity\LogAnalytics;
use Symfony\Component\HttpFoundation\Request;

interface LogAnalyticsRepositoryInterface
{
    public function save(object $data): void;

    public function retrieve(Request $request): void;
}
