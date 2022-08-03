<?php

namespace App\Service\Interfaces;

interface RedisServiceInterface
{
    public function set(string $key, string $value): void;

    public function get(string $key): ?string;
}
