<?php

namespace Tests\Command;

use App\Command\ParseLogFile;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseLogFileCommandTest extends KernelTestCase
{
    protected Application $application;

    private ParseLogFile $parseLogFile;

    private string $resourceDir;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
        $this->resourceDir = $kernel->getResourceDir();
        $this->parseLogFile = new ParseLogFile($kernel);
    }

    public function testInvalidLogFile()
    {
        $content = count($this->getLogLines(false));
        $this->assertEquals(0, $content);
    }

    public function testGetValidLogFileContent()
    {
        $content = count($this->getLogLines());
        $this->assertEquals(20, $content); //assuming that the number of lines in the test log file is 20
    }

    public function testParseFile()
    {
        $content = $this->getLogLines();

        $this->assertNotEmpty($content[0]->service_name);
        $this->assertNotEmpty($content[0]->start_date);
        $this->assertNotEmpty($content[0]->end_date);
        $this->assertNotEmpty($content[0]->status_code);
    }

    public function testExecute()
    {
        $content = count($this->getLogLines());

        $command = $this->application->find('app:parse-log-file');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString("Log file parsed and $content records were inserted into database", $output);
    }

    private function getLogLines(bool $isValid = true): array
    {
        $logFile = $isValid ? $this->resourceDir . '/logs.txt' : $this->resourceDir . '/log.txt';
        $analytics = [];

        return $this->parseLogFile->getLogContent($logFile, $analytics);
    }
}
