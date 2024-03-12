<?php

declare(strict_types=1);

namespace App\Services\Hrm\Interfaces;

use App\Services\Hrm\Employee\ExternalDataInterface;

interface EmployeeDataServiceInterface
{
    public function getEmployeeData(string $userId): ExternalDataInterface;
}
