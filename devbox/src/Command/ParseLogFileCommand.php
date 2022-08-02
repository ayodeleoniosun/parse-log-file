<?php

namespace App\Command;

use App\Service\Interfaces\LogAnalyticsServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name       : 'app:parse-log-file',
    description: 'Parse log file and inserts the data to a database',
)]
class ParseLogFileCommand extends Command
{
    protected string $resourceDir;

    protected Filesystem $filesystem;

    private LogAnalyticsServiceInterface $logService;

    private ParseLogFile $parseLogFile;

    public function __construct(
        KernelInterface $kernel,
        LogAnalyticsServiceInterface $logService,
        ParseLogFile $parseLogFile
    ) {
        parent::__construct();
        $this->resourceDir = $kernel->getResourceDir();
        $this->filesystem = new Filesystem();
        $this->parseLogFile = $parseLogFile;
        $this->logService = $logService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $logFile = $this->resourceDir . '/logs.txt';

        $analytics = [];
        $content = $this->parseLogFile->getLogContent($logFile, $analytics);

        if (count($content) == 0) {
            $io->error('File not parsed. Check if file exists.');

            return Command::FAILURE;
        }

        $countInsertedRecords = $this->logService->store($content);
        $io->success("Log file parsed and $countInsertedRecords records were inserted into database");

        return Command::SUCCESS;
    }
}
