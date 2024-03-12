<?php

declare(strict_types=1);

namespace App\Services\Hrm\Import;

interface BatchResultInterface
{
    public function getAdded(): int;

    public function getUpdated(): int;

    public function getFailed(): int;

    /**
     * @return string[]
     */
    public function getErrors(): array;

    public function incSuccess(bool $isNew): void;

    public function incFailed(?string $error = null): void;

    /**
     * @return array<string, int>
     */
    public function toStats(): array;
}
