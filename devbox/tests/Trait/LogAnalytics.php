<?php

namespace Tests\Trait;

trait LogAnalytics
{
    public function generateLogAnalytics(string $type = 'repository', int $records = 20): array
    {
        $dateTime = $type === 'repository' ? new \DateTime('now') : date('Y-m-d H:i:s');
        $serviceNames = ['user-service', 'invoice-service'];
        $statusCodes = [200, 201, 500];
        $data = [];

        for ($i = 1; $i <= $records; $i++) {
            $serviceNameKey = array_rand($serviceNames);
            $codeKey = array_rand($statusCodes);

            $data[] = (object)[
                'service_name' => $serviceNames[$serviceNameKey],
                'start_date'   => $dateTime,
                'end_date'     => $dateTime,
                'status_code'  => $statusCodes[$codeKey],
                'created_at'   => $dateTime,
                'updated_at'   => $dateTime,
            ];
        }

        return $data;
    }

    public function countFilterAnalyticsByServiceNamesOnly($analytics, array $serviceNames): int
    {
        return count(array_filter($analytics, function ($data) use ($serviceNames) {
            return in_array($data['service_name'], $serviceNames);
        }));
    }

    public function countFilterAnalyticsByServiceNameAndStatusCode($analytics, array $serviceNames, int $statusCode): int
    {
        return count(array_filter($analytics, function ($data) use ($serviceNames, $statusCode) {
            return in_array($data['service_name'], $serviceNames)
                && $data['status_code'] === $statusCode;
        }));
    }

    public function countFilterAnalyticsByServiceNameAndStatusCodeAndStartDate($analytics, array $serviceNames, int $statusCode, \DateTime $dateTime): int
    {
        return count(array_filter($analytics, function ($data) use ($serviceNames, $statusCode, $dateTime) {
            return in_array($data['service_name'], $serviceNames)
                && $data['status_code'] === $statusCode
                && $data['start_date'] == $dateTime;
        }));
    }

    public function countFilterByAllCriteria($analytics, array $serviceNames, int $statusCode, \DateTime $dateTime): int
    {
        return count(array_filter($analytics, function ($data) use ($serviceNames, $statusCode, $dateTime) {
            return in_array($data['service_name'], $serviceNames)
                && $data['status_code'] === $statusCode
                && $data['start_date'] == $dateTime
                && $data['end_date'] == $dateTime;
        }));
    }
}
