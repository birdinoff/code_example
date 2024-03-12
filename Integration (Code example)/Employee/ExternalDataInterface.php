<?php

declare(strict_types=1);

namespace App\Services\Hrm\Employee;

interface ExternalDataInterface
{
    public function getPosition(): string;

    public function getLevel(): string;

    public function getLocation(): string;

    /**
     * @return array<string, string>
     */
    public function toArray(): array;
}
