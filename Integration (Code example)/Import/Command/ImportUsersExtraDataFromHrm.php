<?php

declare(strict_types=1);

namespace App\Services\Hrm\Import\Command;

use App\Models\User\User;
use App\Services\Hrm\Import\BatchResult;
use App\Services\Hrm\Import\BatchResultInterface;
use App\Services\Hrm\Import\CommandInterface;

class ImportUsersExtraDataFromHrm implements CommandInterface
{
    private const DEFAULT_IDENTIFIER_FIELD = 'hrm_reference_id';

    /**
     * @var array<string, string>
     */
    private array $fieldsMap = [
        'hrm_reference_id' => 'id',
        'hrm_user_name' => 'userName',
        'hrm_image_url' => 'image_url',
        'email' => 'email',
    ];

    /**
     * @param array<int, array<string, string>> $data
     * @param array<string, string> $config
     */
    public function execute(array $data, array $config = []): BatchResultInterface
    {
        $importResult = new BatchResult();

        foreach ($data as $employeeRow) {
            $user = $this->loadUser($employeeRow, $config);
            $isNew = $user === null;
            if ($isNew) {
                $user = new User(['hrm_reference_id' => $employeeRow['id']]);
            }

            try {
                $this->populateEmployeeData($user, $employeeRow)
                    ->save();
                $importResult->incSuccess($isNew);
            } catch (\Exception $exception) {
                $importResult->incFailed(
                    'Import of User ' . $user->email . ' failed. Error message: ' . $exception->getMessage()
                );
            }

        }

        return $importResult;
    }

    /**
     * @param array<string, string> $employeeData
     */
    private function populateEmployeeData(User $user, array $employeeData): User
    {
        $user->name = $this->getUserName($employeeData);
        $user->email = $employeeData['email'];
        $user['hrm_reference_id'] = $employeeData['id'];
        $user->setTranslation('hrm_user_name', 'en', $employeeData['firstNameEn'] . ' ' . $employeeData['lastNameEn']);
        $user->setTranslation('hrm_user_name', 'ru', $employeeData['firstNameRu'] . ' ' . $employeeData['lastNameRu']);
        $user['hrm_image_url'] = $employeeData['employeeAdditionalInfo']['linkProfilePicture'];
        return $user;
    }

    /**
     * @param array<string, string> $employee
     * @param array<string, string> $config
     */
    private function loadUser(array $employee, array $config): ?User
    {
        $identifierField = $config['identifier_field'] ?? self::DEFAULT_IDENTIFIER_FIELD;
        $mappedField = $this->fieldsMap[$identifierField] ?? $identifierField;

        if (!isset($employee[$mappedField])) {
            throw new \LogicException('identifier_field specified incorrectly.');
        }

        return User::where($identifierField, $employee[$mappedField])
            ->first();
    }

    /**
     * @param array<string, string> $employeeData
     */
    private function getUserName(array $employeeData): string
    {
        $nameKeys = array_flip(['firstNameEn', 'lastNameEn']);

        $nameParts = array_intersect_key($employeeData, $nameKeys);
        $nameParts = array_filter($nameParts);

        return implode(' ', $nameParts);
    }
}
