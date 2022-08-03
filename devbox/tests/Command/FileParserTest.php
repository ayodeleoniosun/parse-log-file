<?php

namespace Tests\Command;

use App\Command\FileParser;
use App\Service\Interfaces\RedisServiceInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileParserTest extends KernelTestCase
{
    protected Application $application;

    private FileParser $fileParser;

    private string $resourceDir;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
        $this->resourceDir = $kernel->getResourceDir();
        $redis = $this->createMock(RedisServiceInterface::class);
        $this->fileParser = new FileParser($kernel, $redis);
    }

    public function testInvalidLogFile()
    {
        $count = 0;

        foreach ($this->getLogLines(false) as $contents) {
            $count = count($contents);
        }

        $this->assertEquals(0, $count);
    }

    public function testGetValidLogFileContent()
    {
        $count = 0;
        $data = [];

        foreach ($this->getLogLines() as $contents) {
            $data = $contents[0];
            $count = count($contents);
        }

        $this->assertNotEmpty($data->service_name);
        $this->assertNotEmpty($data->start_date);
        $this->assertNotEmpty($data->end_date);
        $this->assertNotEmpty($data->status_code);
        $this->assertEquals(20, $count); //assuming that the number of lines in the test log file is 20
    }

    private function getLogLines(bool $isValid = true): \Generator
    {
        $logFile = $isValid ? $this->resourceDir . '/logs.txt' : $this->resourceDir . '/log.txt';
        $this->fileParser->setFilePath($logFile);

        $analytics = [];

        return $this->fileParser->getLogContent($analytics);
    }
}
