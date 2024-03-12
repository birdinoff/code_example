<?php

declare(strict_types=1);

namespace App\Services\Hrm\Interfaces;

interface TranslationServiceInterface
{
    public function translate(string $attribute, string $uiid): string;
}
