<?php

declare(strict_types=1);

namespace App\Services\Hrm;

use App\Models\User\User;
use App\Services\Hrm\Interfaces\EmployeeDataServiceInterface;
use App\Services\Hrm\Interfaces\UsersExtraDataServiceImportInterface;

class UsersExtraDataImportService implements UsersExtraDataServiceImportInterface
{
    public function __construct(
        private readonly EmployeeDataServiceInterface $dataService
    ) {
    }

    public function importUsersExtraData(): void
    {
        $users = User::all()->where('hrm_reference_id', '!=', null);

        foreach ($users as $user) {
            $externalData = $this->dataService->getEmployeeData($user->id);

            $user->setTranslation('hrm_job_title', $externalData->getPosition(), 'en');
            $user->setTranslation('hrm_division', $externalData->getLevel(), 'en');
            $user->setTranslation('hrm_location', $externalData->getLocation(), 'en');
            $user->save();
        }
    }
}
