<?php

namespace Tests\Trait;

trait LogAnalytics
{
    public function generateLogAnalytics($type): array
    {
        $dateTime = $type === 'repository' ? new \DateTime('now') : date('Y-m-d H:i:s');
        $serviceNames = ['user-service', 'invoice-service'];
        $statusCodes = [200, 201, 500];
        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $serviceNameKey = array_rand($serviceNames);
            $codeKey = array_rand($statusCodes);

            $data[] = (object) [
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
}
