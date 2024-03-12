<?php

declare(strict_types=1);

namespace App\Services\Hrm\HttpClient;

class EmployeesClient extends Client
{
    private const ENDPOINT = '/api/employee-management/api/v1/employees';

    /**
     * @return array<string, array<string, string>>
     */
    public function fetchEmployee(int $employeeId): array
    {
        /** @var array<string, array<string, string>> $employee */
        $employee = $this->request()
            ->get(
                config('services.hrm.base_url') . self::ENDPOINT . '/' . $employeeId,
                ['loadDependency' => true]
            )
            ->json();

        return $employee;
    }
}
