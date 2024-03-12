<?php

declare(strict_types=1);

namespace App\Services\Hrm\Interfaces;

use App\Services\Hrm\Import\StepInterface;

interface UsersImportServiceInterface
{
    public function importUsers(): void;

    public function importUsersWithProgress(): \Generator;

    public function getInitialImportProgressState(): StepInterface;
}
