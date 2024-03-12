<?php

declare(strict_types=1);

namespace App\Services\Hrm;

use App\Services\Hrm\HttpClient\DictionariesClient;
use App\Services\Hrm\Interfaces\TranslationServiceInterface;

class TranslationService implements TranslationServiceInterface
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $translationsMaps = [];

    public function __construct(
        protected readonly DictionariesClient $dictionariesClient
    ) {
    }

    public function translate(string $attribute, string $uiid): string
    {
        $translationMap = $this->getTranslationMap($attribute);

        return $translationMap[$uiid] ?? $uiid;
    }

    /**
     * @return array<string, string>
     */
    private function getTranslationMap(string $attribute): array
    {
        if (!isset($this->translationsMaps[$attribute])) {
            $translations = $this->dictionariesClient->fetchTranslations([$attribute]);
            $this->translationsMaps[$attribute] = array_reduce(
                $translations[$attribute] ?? [],
                function (array $map, array $translation) {
                    $map[$translation['valueId']] = $translation['translation'];

                    return $map;
                },
                []
            );
        }

        return $this->translationsMaps[$attribute] ?? [];
    }
}
