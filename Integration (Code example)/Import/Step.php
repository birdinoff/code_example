<?php

declare(strict_types=1);

namespace App\Services\Hrm\Import;

class Step implements StepInterface
{
    public function __construct(
        private readonly BatchResultInterface $batchResult,
        private readonly int $processedItems,
        private readonly int $totalItems
    ) {
    }

    public function getBatchResult(): BatchResultInterface
    {
        return $this->batchResult;
    }

    public function getProcessedItems(): int
    {
        return $this->processedItems;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }
}
