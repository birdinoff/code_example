<?php

declare(strict_types=1);

namespace App\Services\Hrm\Import;

interface CommandInterface
{
    /**
     * @param array<int, array<string, string>> $data
     * @param array<string, string> $config
     */
    public function execute(array $data, array $config = []): BatchResultInterface;
}
