<?php

namespace Modules\Localization\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

/**
 * Builds the JavaScript translation bundles from the PHP lang files.
 *
 *   resources/js/lang/{locale}.json        admin (Inertia) surface
 *   resources-site/js/lang/{locale}.json   storefront surface
 *
 * Keys keep the exact shape used by __()/trans(): "common.buttons.save" and
 * "cart::site.empty_title", so the front-end loader is a drop-in mirror of the
 * server-side translator.
 */
class LocalizationExportCommand extends Command
{
    /**
     * Translation groups that belong to each surface. App groups live in
     * lang/{locale}; module groups live in modules/{Module}/lang/{locale}
     * under the module's view namespace.
     *
     * @var array<string, array{app: array<int, string>, module: array<int, string>}>
     */
    protected const SURFACES = [
        'admin' => [
            'app' => ['common'],
            'module' => ['admin', 'enums', 'messages', 'mail'],
        ],
        'site' => [
            'app' => ['common'],
            'module' => ['site', 'enums'],
        ],
    ];

    protected $signature = 'localization:export
        {--surface=all : Which bundle to build (admin, site or all)}';

    protected $description = 'Export PHP translation files to JSON bundles consumed by the front-ends';

    public function handle(): int
    {
        $requested = (string) $this->option('surface');
        $surfaces = $requested === 'all' ? array_keys(self::SURFACES) : [$requested];

        foreach ($surfaces as $surface) {
            if (! array_key_exists($surface, self::SURFACES)) {
                $this->components->error("Unknown surface [$surface]. Use admin, site or all.");

                return self::FAILURE;
            }
        }

        foreach ($surfaces as $surface) {
            foreach (config('localization.supported') as $locale => $meta) {
                $path = $this->targetPath($surface, $locale);
                $lines = $this->lines($surface, $locale);

                File::ensureDirectoryExists(dirname($path));
                File::put(
                    $path,
                    json_encode($lines, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL
                );

                $this->components->twoColumnDetail(
                    "exported [$surface/$locale]",
                    count($lines).' keys -> '.str_replace(base_path().'/', '', $path)
                );
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string, string>
     */
    protected function lines(string $surface, string $locale): array
    {
        $groups = self::SURFACES[$surface];
        $lines = [];

        foreach ($groups['app'] as $group) {
            $lines = array_merge($lines, $this->flatten(app()->langPath($locale.'/'.$group.'.php'), $group));
        }

        foreach ($this->moduleLangPaths() as $namespace => $hint) {
            foreach ($groups['module'] as $group) {
                $lines = array_merge($lines, $this->flatten($hint.'/'.$locale.'/'.$group.'.php', $namespace.'::'.$group));
            }
        }

        ksort($lines);

        return $lines;
    }

    /**
     * Registered module lang namespaces (namespace == view namespace).
     *
     * @return array<string, string>
     */
    protected function moduleLangPaths(): array
    {
        $namespaces = app('translation.loader')->namespaces();

        return is_array($namespaces) ? $namespaces : [];
    }

    /**
     * @return array<string, string>
     */
    protected function flatten(string $path, string $prefix): array
    {
        if (! File::exists($path)) {
            return [];
        }

        $lines = [];

        foreach (Arr::dot(File::getRequire($path)) as $key => $value) {
            if (! is_string($value)) {
                continue;
            }

            $lines[$prefix.'.'.$key] = $value;
        }

        return $lines;
    }

    protected function targetPath(string $surface, string $locale): string
    {
        return $surface === 'admin'
            ? resource_path('js/lang/'.$locale.'.json')
            : base_path('resources-site/js/lang/'.$locale.'.json');
    }
}
