<?php

declare(strict_types=1);

namespace App\Services\Hrm;

use App\Services\Hrm\HttpClient\EmployeesSearchClient;
use App\Services\Hrm\Import\BatchResult;
use App\Services\Hrm\Import\BatchResultInterface;
use App\Services\Hrm\Import\Command\ImportUsersFromHrm;
use App\Services\Hrm\Import\Step;
use App\Services\Hrm\Import\StepInterface;
use App\Services\Hrm\Interfaces\UsersImportServiceInterface;

class UsersImportService implements UsersImportServiceInterface
{
    /**
     * @var array<string, string>
     */
    private array $importConfig = [
        'identifier_field' => 'email',
    ];

    public function __construct(
        private readonly EmployeesSearchClient $employeesSearchClient,
        private readonly ImportUsersFromHrm $importUsersFromHrmCommand
    ) {
    }

    public function importUsersWithProgress(): \Generator
    {
        /** @var array<string, array<int, array<string, string>>> $page */
        foreach ($this->fetchAllHrmEmployeesPages() as $page) {
            $importResult = $this->importUsersFromHrmCommand->execute(
                $page['content'] ?? [],
                $this->importConfig
            );

            yield $this->progressPage($page, $importResult);
        }
    }

    public function getInitialImportProgressState(): StepInterface
    {
        return $this->progressPage($this->employeesSearchClient->fetchPage());
    }

    public function importUsers(): void
    {
        /** @var array<string, array<int, array<string, string>>> $page */
        foreach ($this->fetchAllHrmEmployeesPages() as $page) {
            $this->importUsersFromHrmCommand->execute(
                $page['content'] ?? [],
                $this->importConfig
            );
        }
    }

    private function fetchAllHrmEmployeesPages(): \Generator
    {
        $firstPage = $this->employeesSearchClient->fetchPage();

        yield $firstPage;

        /** @var int|null $totalPages */
        $totalPages = $firstPage['totalPages'] ?? null;
        if ($totalPages !== null) {
            $pageCount = 1;
            while ($pageCount < $totalPages) {
                yield $this->employeesSearchClient->fetchPage($pageCount++);
            }
        }
    }

    /**
     * @param array<string, array<int, array<string, string>>> $page
     */
    private function progressPage(array $page, ?BatchResultInterface $batchResult = null): StepInterface
    {
        if ($batchResult === null) {
            $batchResult = new BatchResult();
        }

        return new Step(
            $batchResult,
            EmployeesSearchClient::DEFAULT_PAGE_SIZE,
            (int) $page['totalElements']
        );
    }
}
