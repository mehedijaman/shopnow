<?php

namespace Modules\Localization\Traits;

/**
 * Adds a persisted, validated locale preference to an authenticatable model.
 *
 * The raw column value is never trusted: it is checked against
 * config('localization.supported') before it is ever used.
 *
 * @property  string|null  locale
 */
trait HasLocalePreference
{
    /**
     * The saved locale when it is a supported one, otherwise null.
     */
    public function preferredLocale(): ?string
    {
        $locale = $this->getAttribute('locale');

        if (! is_string($locale) || $locale === '') {
            return null;
        }

        return array_key_exists($locale, config('localization.supported', [])) ? $locale : null;
    }

    /**
     * Persist a locale preference. Returns false when the locale is not supported.
     */
    public function updatePreferredLocale(string $locale): bool
    {
        if (! array_key_exists($locale, config('localization.supported', []))) {
            return false;
        }

        if ($this->getAttribute('locale') === $locale) {
            return true;
        }

        $this->setAttribute('locale', $locale);
        $this->saveQuietly();

        return true;
    }
}
