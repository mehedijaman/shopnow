<?php

namespace Modules\Localization\Traits;

/**
 * Opt-in normalisation of native (Bangla) digits for specific form fields.
 *
 * Apply it from a FormRequest::prepareForValidation() for numeric fields only
 * (phone numbers, PINs, …). Never blanket-convert request input: a Bangla
 * address legitimately contains Western digits and must not be corrupted.
 */
trait NormalizesBanglaDigits
{
    /**
     * @param  string|array<int, string>  $fields
     */
    protected function normalizeBanglaDigits(string|array $fields): void
    {
        foreach ((array) $fields as $field) {
            $value = $this->input($field);

            if (is_string($value) && $value !== '') {
                $this->merge([$field => to_western_digits($value)]);
            }
        }
    }
}
