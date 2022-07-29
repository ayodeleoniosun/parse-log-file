<?php

namespace App\Command;

use App\Service\Interfaces\LogAnalyticsServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

    public function __construct(KernelInterface $kernel, LogAnalyticsServiceInterface $logService)
    {
        parent::__construct();
        $this->resourceDir = $kernel->getResourceDir();
        $this->filesystem = new Filesystem();
        $this->logService = $logService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $logFile = $this->resourceDir . '/logs.txt';
        $fileExists = $this->filesystem->exists($logFile);

        if ($fileExists) {
            $handle = fopen($logFile, 'r');

            if (!$handle) {
                return 0;
            }

            $analytics = [];

            while (!feof($handle)) {
                $line = fgets($handle);

                list($serviceName, $startDateAndTime, $statusCode) = $this->parseFileContent($line);

                $analytics[] = (object)[
                    "service_name" => strtolower($serviceName),
                    "start_date"   => $startDateAndTime,
                    "end_date"     => $startDateAndTime,
                    "status_code"  => $statusCode
                ];
            }

            foreach ($analytics as $data) {
                $this->logService->store($data);
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function parseFileContent($line): array
    {
        //split each line by - - and get the array elements
        $splitLine = explode("- -", $line);
        list ($serviceName, $others) = array_map('trim', $splitLine);

        //split others (everything after the service name) and get their individual elements
        $splitOthers = explode(" ", $others);
        list ($startDate, , $httpMethod, $endpoint, $protocol, $statusCode) = $splitOthers;

        //get start date by removing the first letter ([) and
        // Use the index of the first occurrence of colon (:) to get the start date in this format (d/M/Y)

        $getStartDateAndTime = substr($startDate, 1);
        $index = strpos($getStartDateAndTime, ":");
        $startDate = substr($getStartDateAndTime, 0, $index);
        $startTime = substr($getStartDateAndTime, $index); //get start time in format (H:i:s)
        $startTime = substr($startTime, 1);

        // explode the start date, convert the month date format to number e.g Aug to 08
        // and concatenate with time
        $splitStartDate = explode("/", $startDate);
        list($day, $month, $year) = $splitStartDate;
        $month = date('m', strtotime($month));

        $startDateAndTime = $year . "-" . $month . "-" . $day . " " . $startTime;

        return [$serviceName, $startDateAndTime, $statusCode];
    }
}
