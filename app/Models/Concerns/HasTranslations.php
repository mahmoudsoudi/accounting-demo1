<?php

namespace App\Models\Concerns;

trait HasTranslations
{
    public function getTranslation(string $field, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $translations = $this->{"{$field}_translations"} ?? [];

        if (! empty($translations[$locale])) {
            return $translations[$locale];
        }

        if (! empty($translations['en'])) {
            return $translations['en'];
        }

        return $this->attributes[$field] ?? '';
    }

    public function setTranslationField(string $field, array $translations): void
    {
        $filtered = array_filter(
            $translations,
            fn ($value) => $value !== null && $value !== ''
        );

        $this->{"{$field}_translations"} = $filtered;

        if (! empty($filtered['en'])) {
            $this->attributes[$field] = $filtered['en'];
        } elseif ($filtered !== []) {
            $this->attributes[$field] = (string) reset($filtered);
        }
    }
}
