<?php

declare(strict_types=1);

namespace App\Services\Hrm\HttpClient;

class EmployeesSearchClient extends Client
{
    public const DEFAULT_PAGE_SIZE = 100;

    private const ENDPOINT = '/api/employee-management/api/v1/employees/v2/search';

    /**
     * @return array<string, array<int, array<string, string>>>
     */
    public function fetchPage(int $pageNum = 0): array
    {
        /** @var array<string, array<int, array<string, string>>> $page */
        $page = $this->request()
            ->withQueryParameters(
                [
                    'page' => $pageNum,
                    'size' => self::DEFAULT_PAGE_SIZE,
                    'sort' => [
                        'id asc',
                    ],
                ]
            )
            ->post(
                config('services.hrm.base_url') . self::ENDPOINT,
                [
                    'dismissalStatus' => [
                        'equals' => 'ACTUAL',
                    ],
                    'haveEmptyLanguageLevelId' => true,
                ]
            )
            ->json();

        return $page;
    }
}
