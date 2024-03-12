<?php

declare(strict_types=1);

namespace App\Services\Hrm;

use App\Models\User\User;
use App\Services\Hrm\Employee\ExternalData;
use App\Services\Hrm\Employee\ExternalDataInterface;
use App\Services\Hrm\HttpClient\EmployeesClient;
use App\Services\Hrm\Interfaces\EmployeeDataServiceInterface;
use App\Services\Hrm\Interfaces\TranslationServiceInterface;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Locale;

class EmployeeDataService implements EmployeeDataServiceInterface
{
    public function __construct(
        private readonly EmployeesClient $employeesClient,
        private readonly TranslationServiceInterface $translationService,
    ) {
    }

    public function getEmployeeData(string $userId): ExternalDataInterface
    {
        return $this->fetchEmployeeData($userId);
    }

    private function fetchEmployeeData(string $userId): ExternalDataInterface
    {
        $employeeId = $this->getEmployeeId($userId);
        $employee = $this->employeesClient->fetchEmployee($employeeId);

        /** @var string[]|string[][]|null $employeeDto */

        /**
         * @var array{
         *     employeeAdditionalInfo: array{jobTitleId: string, linkProfilePicture: string},
         *     professionalLevelId: string,
         *     location: array{countryName: string, cityName: string}
         * }|null $employeeDto
         */
        $employeeDto = $employee['data']['employeeDto'] ?? null;

        return new ExternalData(
            $employeeDto
                ? $this->translationService->translate(
                    'jobTitle',
                    $employeeDto['employeeAdditionalInfo']['jobTitleId']
                )
                : '',
            $employeeDto
                ? $this->translationService->translate(
                    'professionalLevel',
                    $employeeDto['professionalLevelId']
                )
                : '',
            $employeeDto ? $this->formatLocation($employeeDto['location']) : '',
        );
    }

    private function getEmployeeId(string $userId): int
    {
        $user = User::where('id', $userId)
            ->first();
        if ($user === null || !$user->hrm_reference_id) {
            throw new InvalidArgumentException('Specified user has not been synchronized with HRM.');
        }

        return (int) $user->hrm_reference_id;
    }

    /**
     * @param array<string, string> $location
     */
    private function formatLocation(array $location): string
    {
        $countryName = $location['countryName'] ?? '';
        if (class_exists(Locale::class, false) && !empty($countryName)) {
            $countryName = Locale::getDisplayRegion('-' . $countryName, 'ru');
        }

        return $countryName . ', ' . ($location['cityName'] ?? '');
    }
}
