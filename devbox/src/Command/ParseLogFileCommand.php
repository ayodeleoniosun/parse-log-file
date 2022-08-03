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

    private FileParser $fileParser;

    protected $io;

    public function __construct(
        KernelInterface              $kernel,
        LogAnalyticsServiceInterface $logService,
        FileParser                   $fileParser
    ) {
        parent::__construct();
        $this->resourceDir = $kernel->getResourceDir();
        $this->filesystem = new Filesystem();
        $this->fileParser = $fileParser;
        $this->logService = $logService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->info('Parsing file ...');

        $logFile = $this->resourceDir . '/logs.txt';
        $fileHash = $this->fileParser->getFileNameHash($logFile);

        $fileExists = $this->filesystem->exists($logFile);

        if (!$fileExists) {
            $this->io->error('File not found.');

            return Command::FAILURE;
        }

        $analytics = [];
        $this->fileParser->setFilePath($logFile);

        $countInsertedRecords = 0;

        foreach ($this->fileParser->getLogContent($analytics) as $data) {
            $countInsertedRecords += $this->logService->store($data);
            $this->fileParser->setFileLastLine($fileHash, $countInsertedRecords);
        }

        $this->io->success("Log file parsed and $countInsertedRecords records were inserted into database");

        return Command::SUCCESS;
    }

    /*
        Assumptions
        1. File content are not edited randomly
        2. Insertions are sequentials
        3. New logs are appended to file from the bottom
    */
}
