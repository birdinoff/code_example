<?php

declare(strict_types=1);

namespace App\Services\Hrm\Employee;

class ExternalData implements ExternalDataInterface
{
    public function __construct(
        private readonly string $position,
        private readonly string $level,
        private readonly string $location,
    ) {
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'position' => $this->getPosition(),
            'location' => $this->getLocation(),
            'level' => $this->getLevel(),
        ];
    }
}
