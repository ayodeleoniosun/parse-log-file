<?php

namespace App\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class ParseLogFile
{
    protected string $resourceDir;

    protected Filesystem $filesystem;

    public function __construct(KernelInterface $kernel)
    {
        $this->resourceDir = $kernel->getResourceDir();
        $this->filesystem = new Filesystem();
    }

    public function getLogContent($file, array &$analytics): array
    {
        $handle = fopen($file, 'r');

        if (!$handle) {
            return [];
        }

        while (!feof($handle)) {
            $line = fgets($handle);

            list($serviceName, $startDateAndTime, $statusCode) = $this->parseFileContent($line);

            $analytics[] = (object)[
                'service_name' => strtolower($serviceName),
                'start_date'   => $startDateAndTime,
                'end_date'     => $startDateAndTime,
                'status_code'  => $statusCode,
            ];
        }

        return $analytics;
    }

    public function parseFileContent($line): array
    {
        // split each line by - - and get the array elements
        $splitLine = explode('- -', $line);
        list($serviceName, $others) = array_map('trim', $splitLine);

        // split others (everything after the service name) and get their individual elements
        $splitOthers = explode(' ', $others);
        list($startDate, , $httpMethod, $endpoint, $protocol, $statusCode) = $splitOthers;

        // get start date by removing the first letter ([) and
        // Use the index of the first occurrence of colon (:) to get the start date in this format (d/M/Y)

        $getStartDateAndTime = substr($startDate, 1);
        $index = strpos($getStartDateAndTime, ':');
        $startDate = substr($getStartDateAndTime, 0, $index);
        $startTime = substr($getStartDateAndTime, $index); // get start time in format (H:i:s)
        $startTime = substr($startTime, 1);

        // explode the start date, convert the month date format to number e.g Aug to 08
        // and concatenate with time
        $splitStartDate = explode('/', $startDate);
        list($day, $month, $year) = $splitStartDate;
        $month = date('m', strtotime($month));

        $startDateAndTime = $year . '-' . $month . '-' . $day . ' ' . $startTime;

        return [$serviceName, $startDateAndTime, $statusCode];
    }
}
