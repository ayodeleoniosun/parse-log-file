<?php

namespace App\Command;

use App\Service\Interfaces\RedisServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class FileParser
{
    public string $resourceDir;

    public Filesystem $filesystem;

    public string $filePath;

    public int $currentLine = 0;

    public RedisServiceInterface $redis;

    public function __construct(KernelInterface $kernel, RedisServiceInterface $redis)
    {
        $this->resourceDir = $kernel->getResourceDir();
        $this->filesystem = new Filesystem();
        $this->redis = $redis;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getCurrentLine(): int
    {
        return $this->currentLine;
    }

    public function getFileLastLine(string $hash): int
    {
        return (int) $this->redis->get($hash);
    }

    public function setFileLastLine(string $hash, int $line): void
    {
        $this->redis->set($hash, $line);
    }

    public function getFileLastInsertion(string $hash): int
    {
        return (int) $this->redis->get($hash);
    }

    public function setFileLastInsertion(string $hash, int $insertion): void
    {
        $this->redis->set($hash, $insertion);
    }

    public function getFileNameMd5(string $filePath): string
    {
        return sha1_file($filePath);
    }

    public function getFileNameHash(string $filePath): string
    {
        return sha1_file($filePath);
    }

    public function getLogContent(array &$analytics, int $batchLength = 20): array|\Generator
    {
        $fileExists = $this->filesystem->exists($this->filePath);

        if (! $fileExists) {
            yield [];

            return;
        }

        $handle = fopen($this->filePath, 'r');
        $fileHash = $this->getFileNameHash($this->filePath);
        $lastLine = $this->getFileLastLine($fileHash);

        if (! $handle) {
            yield [];

            return;
        }

        $analytics = [];

        while (! feof($handle)) {
            $this->currentLine++;

            if ($this->currentLine < $lastLine) {
                continue;
            }

            $line = fgets($handle);

            $analytics[] = (object) $this->parseLineContent($line);

//            if (count($analytics) >= $batchLength) {
//                //yield $analytics;
//                $analytics = [];
//            }

            $this->currentLine++;
        }

        yield $analytics;
    }

    public function parseLineContent($line): array
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

        return [
            'service_name' => strtolower($serviceName),
            'start_date'   => $startDateAndTime,
            'end_date'     => $startDateAndTime,
            'status_code'  => $statusCode,
        ];
    }
}
