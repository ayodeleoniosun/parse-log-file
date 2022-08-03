<?php

namespace App\Service;

use App\Service\Interfaces\RedisServiceInterface;
use Predis\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class RedisService implements RedisServiceInterface
{
    public string $redisHost;

    public string $redisPort;

    public Client $redis;

    public function __construct(ContainerBagInterface $params)
    {
        $this->redisHost = $params->get('app.redis_host');
        $this->redisPort = $params->get('app.redis_port');

        $this->redis = new Client([
            'host' => $this->redisHost,
            'port' => $this->redisPort
        ]);
    }

    public function set(string $key, string $value): void
    {
        $this->redis->set($key, $value);
    }

    public function get(string $key): ?string
    {
        return $this->redis->get($key);
    }
}
