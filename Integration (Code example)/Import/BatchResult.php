<?php

declare(strict_types=1);

namespace App\Services\Hrm\Import;

class BatchResult implements BatchResultInterface
{
    private int $addedCount = 0;

    private int $updatedCount = 0;

    private int $failedCount = 0;

    /**
     * @var string[]
     */
    private array $errors = [];

    public function getAdded(): int
    {
        return $this->addedCount;
    }

    public function getUpdated(): int
    {
        return $this->updatedCount;
    }

    public function getFailed(): int
    {
        return $this->failedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function incSuccess(bool $isNew): void
    {
        if ($isNew) {
            $this->addedCount++;
        } else {
            $this->updatedCount++;
        }
    }

    public function incFailed(?string $error = null): void
    {
        $this->failedCount++;

        if ($error) {
            $this->errors[] = $error;
        }
    }

    /**
     * @return array<string, int>
     */
    public function toStats(): array
    {
        return [
            'added' => $this->getAdded(),
            'updated' => $this->getUpdated(),
            'failed' => $this->getFailed(),
        ];
    }
}
