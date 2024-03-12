<?php

declare(strict_types=1);

namespace App\Services\Hrm\Import;

interface StepInterface
{
    public function getBatchResult(): BatchResultInterface;

    public function getProcessedItems(): int;

    public function getTotalItems(): int;
}
