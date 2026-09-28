<?php

namespace Modules\Localization\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Translation\FileLoader;
use ReflectionProperty;
use Symfony\Component\Finder\Finder;

/**
 * Static verification of the translation catalogue.
 *
 * Errors (exit 1):
 *   - key parity inside a lang unit that exists in the target locale
 *   - placeholder parity between locales
 *   - empty translation values
 *   - keys referenced from PHP/Blade that do not exist in the fallback locale
 *
 * Pending (exit 1 only with --strict):
 *   - lang units that have not been translated yet
 *
 * Warnings (exit 1 only with --strict):
 *   - English-sentence / missing keys referenced from JavaScript
 *   - lang keys that no source file references
 */
class LocalizationCheckCommand extends Command
{
    /**
     * Units whose keys are looked up dynamically by the framework, so an
     * "unused key" report for them would be pure noise.
     */
    protected const DYNAMIC_UNITS = ['validation', 'auth', 'passwords', 'pagination'];

    /**
     * Directories holding non-shipped sources, skipped when scanning for
     * translation references.
     */
    protected const IGNORED_DIRECTORIES = ['Tests', 'tests', 'stubs'];

    protected $signature = 'localization:check
        {--strict : Fail on pending translations and warnings as well as errors}';

    protected $description = 'Verify translation key parity, placeholders and referenced keys';

    /** @var array<int, string> */
    protected array $errors = [];

    /** @var array<int, string> */
    protected array $pending = [];

    /** @var array<int, string> */
    protected array $warnings = [];

    /** @var array<string, array<string, string>> locale => full key => value */
    protected array $catalogue = [];

    /** @var array<string, array<string, string>> locale => unit => prefixed key => value */
    protected array $units = [];

    /** @var array<string, string> path => source with comments stripped */
    protected array $sources = [];

    public function handle(): int
    {
        $locales = array_keys(config('localization.supported', ['en' => []]));
        $fallback = (string) config('app.fallback_locale', 'en');

        foreach ($locales as $locale) {
            $this->units[$locale] = $this->unitsFor($locale);
            $this->catalogue[$locale] = $this->flattenCatalogue($this->units[$locale]);
        }

        $this->checkParity($locales, $fallback);
        $this->checkEmptyValues($locales);
        $this->checkPlaceholderParity($locales, $fallback);
        $this->checkPhpReferences($fallback);
        $this->checkJsReferences($fallback);
        $this->checkUnusedKeys($locales, $fallback);

        $this->report();

        $failed = $this->errors !== []
            || ($this->option('strict') && ($this->pending !== [] || $this->warnings !== []));

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $locales
     */
    protected function checkParity(array $locales, string $fallback): void
    {
        foreach ($locales as $locale) {
            if ($locale === $fallback) {
                continue;
            }

            foreach ($this->units[$fallback] as $unit => $keys) {
                if (! isset($this->units[$locale][$unit])) {
                    $this->pending[] = "[$locale] $unit: not translated yet";

                    continue;
                }

                $target = $this->units[$locale][$unit];

                foreach (array_diff(array_keys($keys), array_keys($target)) as $key) {
                    $this->errors[] = "[$locale] $key: missing from $unit";
                }

                foreach (array_diff(array_keys($target), array_keys($keys)) as $key) {
                    $this->errors[] = "[$locale] $key: does not exist in $fallback";
                }
            }
        }
    }

    /**
     * @param  array<int, string>  $locales
     */
    protected function checkEmptyValues(array $locales): void
    {
        foreach ($locales as $locale) {
            foreach ($this->catalogue[$locale] as $key => $value) {
                if (trim($value) === '') {
                    $this->errors[] = "[$locale] $key: empty translation";
                }
            }
        }
    }

    /**
     * @param  array<int, string>  $locales
     */
    protected function checkPlaceholderParity(array $locales, string $fallback): void
    {
        $expected = [];

        foreach ($this->catalogue[$fallback] as $key => $value) {
            $expected[$key] = $this->placeholders($value);
        }

        foreach ($locales as $locale) {
            if ($locale === $fallback) {
                continue;
            }

            foreach ($this->catalogue[$locale] as $key => $value) {
                if (! isset($expected[$key])) {
                    continue;
                }

                $actual = $this->placeholders($value);
                $diff = array_unique(array_merge(
                    array_diff($expected[$key], $actual),
                    array_diff($actual, $expected[$key])
                ));

                if ($diff !== []) {
                    $this->errors[] = sprintf(
                        '[%s] %s: placeholder mismatch (%s)',
                        $locale,
                        $key,
                        implode(', ', $diff)
                    );
                }
            }
        }
    }

    protected function checkPhpReferences(string $fallback): void
    {
        foreach ($this->sourceFiles(['php', 'blade.php']) as $file) {
            foreach ($this->literalKeys($file) as $literal) {
                if ($this->keyExists($literal, $fallback)) {
                    continue;
                }

                if (! $this->isKeyShaped($literal)) {
                    $this->errors[] = sprintf(
                        '[php] "%s" in %s: English-sentence key, move it into a lang file',
                        $literal,
                        $this->relative($file)
                    );

                    continue;
                }

                $this->errors[] = sprintf(
                    '[php] "%s" in %s: key not found',
                    $literal,
                    $this->relative($file)
                );
            }
        }
    }

    protected function checkJsReferences(string $fallback): void
    {
        foreach ($this->sourceFiles(['js', 'vue', 'mjs'], ['resources/js', 'resources-site/js']) as $file) {
            foreach ($this->literalKeys($file) as $literal) {
                if (! $this->isKeyShaped($literal)) {
                    $this->warnings[] = sprintf(
                        '[js] "%s" in %s: English-sentence key, convert to a dotted key',
                        $literal,
                        $this->relative($file)
                    );

                    continue;
                }

                if (! $this->keyExists($literal, $fallback)) {
                    $this->warnings[] = sprintf(
                        '[js] "%s" in %s: key not found',
                        $literal,
                        $this->relative($file)
                    );
                }
            }
        }
    }

    /**
     * @param  array<int, string>  $locales
     */
    protected function checkUnusedKeys(array $locales, string $fallback): void
    {
        $referenced = [];

        foreach ($this->sourceFiles(['php', 'blade.php', 'js', 'vue', 'mjs']) as $file) {
            foreach ($this->literalKeys($file) as $literal) {
                $referenced[$literal] = true;
            }
        }

        $unused = [];

        foreach ($this->units[$fallback] as $unit => $keys) {
            if (in_array($this->baseUnit($unit), self::DYNAMIC_UNITS, true)) {
                continue;
            }

            foreach (array_keys($keys) as $key) {
                if (! isset($referenced[$key])) {
                    $unused[] = $key;
                }
            }
        }

        if ($unused === []) {
            return;
        }

        $this->warnings[] = sprintf(
            '%d key(s) in %s are never referenced (run with -vv to list them)',
            count($unused),
            $fallback
        );

        if ($this->output->isVeryVerbose()) {
            sort($unused);

            foreach ($unused as $key) {
                $this->warnings[] = "[unused] $key";
            }
        }
    }

    protected function report(): void
    {
        foreach ($this->errors as $message) {
            $this->components->error($message);
        }

        foreach ($this->pending as $message) {
            $this->components->warn($message);
        }

        foreach ($this->warnings as $message) {
            $this->components->warn($message);
        }

        $this->newLine();

        $this->components->twoColumnDetail('errors', (string) count($this->errors));
        $this->components->twoColumnDetail('pending translations', (string) count($this->pending));
        $this->components->twoColumnDetail('warnings', (string) count($this->warnings));
    }

    /**
     * @return array<int, string>
     */
    protected function literalKeys(string $file): array
    {
        $pattern = '/(?:\b__|\btrans|\btrans_choice|@lang)\(\s*([\'"])(.+?)\1/s';

        preg_match_all($pattern, $this->source($file), $matches);

        return array_values(array_unique($matches[2] ?? []));
    }

    /**
     * Source with comments removed for plain PHP files, so that examples in
     * docblocks are not mistaken for real calls.
     */
    protected function source(string $file): string
    {
        if (isset($this->sources[$file])) {
            return $this->sources[$file];
        }

        $contents = File::get($file);

        if (str_ends_with($file, '.php') && ! str_ends_with($file, '.blade.php')) {
            $stripped = php_strip_whitespace($file);

            $contents = $stripped === false ? $contents : $stripped;
        }

        return $this->sources[$file] = $contents;
    }

    protected function keyExists(string $literal, string $fallback): bool
    {
        if (str_contains($literal, '::')) {
            [$namespace] = explode('::', $literal, 2);

            if (! array_key_exists($namespace, $this->moduleLangPaths())) {
                return false;
            }
        }

        return isset($this->catalogue[$fallback][$literal]);
    }

    protected function isKeyShaped(string $literal): bool
    {
        return str_contains($literal, '::') || str_contains($literal, '.');
    }

    /**
     * unit name => "unit.key" => value.
     *
     * @return array<string, array<string, string>>
     */
    protected function unitsFor(string $locale): array
    {
        $units = [];

        foreach ($this->rootPaths() as $path) {
            foreach (File::glob($path.'/'.$locale.'/*.php') as $file) {
                $unit = basename($file, '.php');
                $units[$unit] = array_replace_recursive($units[$unit] ?? [], $this->requireFile($file));
            }
        }

        foreach ($this->moduleLangPaths() as $namespace => $hint) {
            foreach (File::glob($hint.'/'.$locale.'/*.php') as $file) {
                $unit = $namespace.'::'.basename($file, '.php');
                $units[$unit] = array_replace_recursive($units[$unit] ?? [], $this->requireFile($file));
            }
        }

        $flattened = [];

        foreach ($units as $unit => $lines) {
            foreach ($this->flatten($lines) as $key => $value) {
                $flattened[$unit][$unit.'.'.$key] = $value;
            }
        }

        return $flattened;
    }

    /**
     * @param  array<string, array<string, string>>  $units
     * @return array<string, string>
     */
    protected function flattenCatalogue(array $units): array
    {
        $catalogue = [];

        foreach ($units as $unit => $lines) {
            foreach ($lines as $key => $value) {
                $catalogue[$key] = $value;
            }
        }

        return $catalogue;
    }

    /**
     * @return array<int, string>
     */
    protected function rootPaths(): array
    {
        $loader = app('translation.loader');

        if (! $loader instanceof FileLoader) {
            return [app()->langPath()];
        }

        $paths = (new ReflectionProperty($loader, 'paths'))->getValue($loader);

        return is_array($paths) ? $paths : [app()->langPath()];
    }

    /**
     * Registered module lang namespaces (namespace == view namespace).
     *
     * Namespaces owned by third-party packages are excluded: their English
     * files would otherwise be reported as never-translated forever, and this
     * application must not depend on strings it does not own.
     *
     * @return array<string, string>
     */
    protected function moduleLangPaths(): array
    {
        $namespaces = app('translation.loader')->namespaces();

        if (! is_array($namespaces)) {
            return [];
        }

        $vendor = realpath(base_path('vendor'));
        $owned = [];

        foreach ($namespaces as $namespace => $hint) {
            $real = (string) realpath($hint);

            if ($vendor === false || ! str_starts_with($real, $vendor)) {
                $owned[$namespace] = $hint;
            }
        }

        return $owned;
    }

    /**
     * @return array<string, mixed>
     */
    protected function requireFile(string $file): array
    {
        $lines = File::getRequire($file);

        return is_array($lines) ? $lines : [];
    }

    /**
     * @param  array<string, mixed>  $array
     * @return array<string, string>
     */
    protected function flatten(array $array): array
    {
        $results = [];

        foreach ($this->dot($array) as $key => $value) {
            if (is_string($value)) {
                $results[$key] = $value;
            }
        }

        return $results;
    }

    /**
     * @param  array<string, mixed>  $array
     * @return array<string, mixed>
     */
    protected function dot(array $array, string $prefix = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            $key = $prefix.$key;

            if (is_array($value) && $value !== [] && ! array_is_list($value)) {
                $results = array_merge($results, $this->dot($value, $key.'.'));
            } else {
                $results[$key] = $value;
            }
        }

        return $results;
    }

    /**
     * @return array<int, string>
     */
    /**
     * Placeholders used by a message, normalised for comparison.
     *
     * The validator accepts `:attribute`, `:Attribute` and `:ATTRIBUTE` as the
     * same placeholder (it upper-cases or capitalises for us), so only that one
     * is matched case-insensitively. Everything else — `:min`, `:max`, `:page`
     * — is replaced verbatim and must match exactly.
     *
     * @return array<int, string>
     */
    protected function placeholders(string $value): array
    {
        preg_match_all('/:([a-zA-Z_][a-zA-Z0-9_]*)/', $value, $matches);

        $placeholders = array_map(
            fn (string $name): string => strtolower($name) === 'attribute' ? 'attribute' : $name,
            $matches[1] ?? []
        );

        $placeholders = array_values(array_unique($placeholders));
        sort($placeholders);

        return $placeholders;
    }

    protected function baseUnit(string $unit): string
    {
        $parts = explode('::', $unit);

        return end($parts);
    }

    /**
     * @param  array<int, string>  $extensions
     * @param  array<int, string>|null  $roots
     * @return array<int, string>
     */
    protected function sourceFiles(array $extensions, ?array $roots = null): array
    {
        $roots = $roots ?? ['app', 'modules', 'resources', 'resources-site', 'routes', 'database'];

        $files = [];

        foreach ($roots as $root) {
            $path = base_path($root);

            if (! File::isDirectory($path)) {
                continue;
            }

            // Tests exercise the translator with deliberate junk — a missing
            // key, an English sentence — and are never shipped UI.
            $finder = (new Finder)->files()->in($path)->ignoreVCS(true)->exclude(self::IGNORED_DIRECTORIES);

            foreach ($extensions as $extension) {
                $finder->name('*.'.$extension);
            }

            foreach ($finder as $file) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    protected function relative(string $path): string
    {
        return str_replace(base_path().'/', '', $path);
    }
}
