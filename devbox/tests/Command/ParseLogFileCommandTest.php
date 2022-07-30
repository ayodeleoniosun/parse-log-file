<?php

namespace Tests\Command;

use App\DataFixtures\LogAnalyticsFixture;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseLogFileCommandTest extends KernelTestCase
{
    protected Application $application;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);
    }

    public function testExecute()
    {
        $command = $this->application->find('app:parse-log-file');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();
    }
}
