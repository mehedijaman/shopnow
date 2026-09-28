<?php

use Carbon\Carbon;
use Modules\Localization\Services\LocaleManager;

if (! function_exists('localization')) {
    /**
     * The locale resolver/application service.
     */
    function localization(): LocaleManager
    {
        return app(LocaleManager::class);
    }
}

if (! function_exists('supported_locales')) {
    /**
     * Supported locales, keyed by locale code.
     *
     * @return array<string, array<string, mixed>>
     */
    function supported_locales(): array
    {
        return localization()->supported();
    }
}

if (! function_exists('current_locale')) {
    function current_locale(): string
    {
        return app()->getLocale();
    }
}

if (! function_exists('html_lang')) {
    /**
     * Value for the <html lang="..."> attribute (BCP 47 style, e.g. en-US).
     */
    function html_lang(): string
    {
        return str_replace('_', '-', current_locale());
    }
}

if (! function_exists('html_dir')) {
    /**
     * Value for the <html dir="..."> attribute, emitted from config.
     */
    function html_dir(): string
    {
        return localization()->dir();
    }
}

if (! function_exists('locale_uses_native_digits')) {
    function locale_uses_native_digits(?string $locale = null): bool
    {
        return localization()->usesNativeDigits($locale);
    }
}

if (! function_exists('to_native_digits')) {
    /**
     * Convert Western digits to the native digits of the current locale.
     *
     * Display only: never apply to input values, URLs, data attributes, IDs or
     * anything else a machine reads back.
     */
    function to_native_digits(string $value, ?string $locale = null): string
    {
        if (! locale_uses_native_digits($locale)) {
            return $value;
        }

        return strtr($value, [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ]);
    }
}

if (! function_exists('to_western_digits')) {
    /**
     * Normalise native (Bangla) digits back to 0-9. Used for form input.
     */
    function to_western_digits(string $value): string
    {
        return strtr($value, [
            '০' => '0',
            '১' => '1',
            '২' => '2',
            '৩' => '3',
            '৪' => '4',
            '৫' => '5',
            '৬' => '6',
            '৭' => '7',
            '৮' => '8',
            '৯' => '9',
        ]);
    }
}

if (! function_exists('format_number')) {
    /**
     * Locale-aware number with Western 3-digit grouping.
     *
     * Under a native-digit locale the digits are transliterated afterwards, so
     * grouping stays comma-every-3 (১,৪৫৬) instead of following bn grouping.
     */
    function format_number(int|float|string $number, ?int $decimals = null): string
    {
        $value = (float) $number;

        $formatted = $decimals === null
            ? number_format($value)
            : number_format($value, $decimals);

        return to_native_digits($formatted);
    }
}

if (! function_exists('format_money')) {
    /**
     * Currency amount for display, e.g. ৳1,456.50 / ৳১,৪৫৬.৫০.
     */
    function format_money(int|float|string $amount, int $decimals = 2, string $symbol = '৳'): string
    {
        return $symbol.format_number($amount, $decimals);
    }
}

if (! function_exists('format_date')) {
    /**
     * Locale-aware date/time formatting.
     *
     * English uses PHP's DateTime::format so existing output stays
     * byte-identical; other locales use Carbon's translated names.
     */
    function format_date(DateTimeInterface|string|null $date, string $format = 'd M Y, h:i A'): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        $date = Carbon::parse($date);
        $locale = current_locale();

        $formatted = $locale === 'en'
            ? $date->format($format)
            : $date->locale($locale)->translatedFormat($format);

        return to_native_digits($formatted, $locale);
    }
}
