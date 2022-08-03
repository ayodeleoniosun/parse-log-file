<?php

namespace Tests\Command;

use App\Command\FileParser;
use App\Service\Interfaces\RedisServiceInterface;
use App\Service\RedisService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseLogFileCommandTest extends KernelTestCase
{
    protected Application $application;

    private FileParser $fileParser;

    private string $resourceDir;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        $this->application = new Application($kernel);
        $this->resourceDir = $kernel->getResourceDir();

        $redis = $this->createMock(RedisServiceInterface::class);
        $this->redisService = $container->get(RedisService::class);

        $this->fileParser = new FileParser($kernel, $redis);
    }

    public function testExecute()
    {
        $content = $this->countLogLines();

        $command = $this->application->find('app:parse-log-file');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString("Log file parsed and $content records were inserted into database", $output);
    }

    private function countLogLines(): int
    {
        $logFile = $this->resourceDir . '/logs.txt';
        $this->fileParser->setFilePath($logFile);

        $analytics = [];

        $count = 0;

        foreach ($this->fileParser->getLogContent($analytics) as $contents) {
            $count = count($contents);
        }

        return $count;
    }
}
