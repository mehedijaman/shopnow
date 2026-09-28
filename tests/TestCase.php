<?php

namespace Tests;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Modules\Localization\Support\MissingTranslationKeys;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Existing assertions were written against English output, and Carbon's
        // locale is process-global, so every test starts from a known state.
        $this->app->setLocale('en');
        Carbon::setLocale('en');
        CarbonImmutable::setLocale('en');
    }

    protected function tearDown(): void
    {
        try {
            $this->assertNoMissingTranslationKeys();
        } finally {
            parent::tearDown();
        }
    }

    /**
     * Any key that resolves in neither the active locale nor the fallback is a
     * real bug: it renders as the raw key in the UI. Framework validation
     * probes are filtered by the recorder itself.
     */
    protected function assertNoMissingTranslationKeys(): void
    {
        if (! $this->app) {
            return;
        }

        $missing = $this->app->make(MissingTranslationKeys::class)->keys();

        $this->assertSame(
            [],
            $missing,
            'Missing translation keys: '.implode(', ', $missing)
        );
    }
}
