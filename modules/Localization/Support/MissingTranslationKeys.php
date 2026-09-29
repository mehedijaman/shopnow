<?php

namespace Modules\Localization\Support;

/**
 * Collects translation keys that were requested but exist in neither the
 * active locale nor the fallback locale.
 *
 * Two classes of lookup are deliberately not recorded, because the
 * application does not own them and `localization:check` cannot see them
 * either:
 *
 *   - validation keys: the framework probes keys that are expected to be
 *     absent (custom messages, empty attribute maps, per-rule lookups).
 *   - sentences instead of keys: framework and package code calls
 *     `__($exception->getMessage())`, `__('Forbidden')`,
 *     `choice('(and :count more error)')` and similar, so any literal that
 *     is not dotted or namespaced is left alone. Keys found in our own PHP,
 *     Blade and JS sources are still reported as errors by
 *     `localization:check`.
 */
class MissingTranslationKeys
{
    /**
     * @var array<string, array{locale: string, replace: array<string, mixed>}>
     */
    protected array $recorded = [];

    /**
     * Returns true only when the lookup was kept, so callers can skip
     * logging lookups this class deliberately ignores.
     */
    public function record(string $key, string $locale, array $replace = []): bool
    {
        if ($this->isIgnored($key)) {
            return false;
        }

        $this->recorded[$key] ??= ['locale' => $locale, 'replace' => $replace];

        return true;
    }

    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->recorded);
    }

    /**
     * @return array<string, array{locale: string, replace: array<string, mixed>}>
     */
    public function all(): array
    {
        return $this->recorded;
    }

    public function isEmpty(): bool
    {
        return $this->recorded === [];
    }

    public function flush(): void
    {
        $this->recorded = [];
    }

    protected function isIgnored(string $key): bool
    {
        if (str_starts_with($key, 'validation.')) {
            return true;
        }

        return ! $this->looksLikeAKey($key);
    }

    /**
     * A key the application owns: `group.item`, `group.item.sub` or
     * `namespace::group.item`.
     */
    protected function looksLikeAKey(string $key): bool
    {
        if (str_contains($key, '::')) {
            return true;
        }

        return preg_match('/^[A-Za-z0-9_-]+(?:\.[A-Za-z0-9_-]+)+$/', $key) === 1;
    }
}
