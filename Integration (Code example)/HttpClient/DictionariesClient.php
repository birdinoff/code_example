<?php

declare(strict_types=1);

namespace App\Services\Hrm\HttpClient;

class DictionariesClient extends Client
{
    private const TRANSLATIONS_FILTER_ENDPOINT = '/api/dictionaries/api/v2/dictionary-translations/filter';

    /**
     * @param string[] $attributes
     * @return array<string, string[][]>
     */
    public function fetchTranslations(array $attributes, bool $useDefaultLanguageOnly = true): array
    {
        /** @var array<string, string[][]> $translations */
        $translations = $this->request()
            ->get(
                config('services.hrm.base_url') . self::TRANSLATIONS_FILTER_ENDPOINT,
                [
                    'filter' => json_encode(['name' => $attributes]),
                    'defaultLanguageOnly' => $useDefaultLanguageOnly,
                ]
            )
            ->json();

        return $translations;
    }
}
