<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Settings\Models\Setting;

class AppearanceSettings
{
    public const COLOR_FIELDS = ['primary', 'accent', 'background', 'surface', 'text', 'muted', 'border', 'topbar', 'topbar_text', 'header', 'header_text', 'footer', 'footer_text'];

    /**
     * @return array<string, mixed>
     */
    public function defaults(string $preset = 'default'): array
    {
        $presets = config('appearance.presets');
        $preset = array_key_exists($preset, $presets) ? $preset : 'default';
        $base = $presets[$preset];

        return [
            'version' => config('appearance.version'), 'preset' => $preset, 'neutral' => $base['neutral'],
            'light' => ['primary' => $base['primary'], 'accent' => $base['accent']] + config('appearance.modes.light'),
            'dark' => ['primary' => $base['primary'], 'accent' => $base['accent']] + config('appearance.modes.dark'),
        ];
    }

    public function initialize(): void
    {
        Setting::firstOrCreate(['group' => 'appearance', 'key' => 'palette'], [
            'value' => json_encode($this->defaults() + ['revision' => 0], JSON_THROW_ON_ERROR),
            'type' => 'json', 'label' => 'Storefront palette', 'is_public' => true, 'sort_order' => 1,
        ]);
    }

    /** @return array<string, mixed> */
    public function forForm(): array
    {
        $stored = Setting::where('group', 'appearance')->where('key', 'palette')->first()?->value;

        return ['palette' => $this->normalize($stored), 'revision' => (int) (is_array($stored) ? ($stored['revision'] ?? 0) : 0)];
    }

    /** @return array<string, mixed> */
    public function metadata(): array
    {
        $presets = [];
        foreach (array_keys(config('appearance.presets')) as $name) {
            $presets[$name] = $this->defaults($name);
        }

        return ['presets' => $presets, 'fields' => self::COLOR_FIELDS];
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [
            'revision' => ['required', 'integer', 'min:0'],
            'palette' => ['required', 'array:version,preset,neutral,light,dark'],
            'palette.version' => ['required', 'integer', Rule::in([config('appearance.version')])],
            'palette.preset' => ['required', Rule::in([...array_keys(config('appearance.presets')), 'custom'])],
            'palette.neutral' => ['required', Rule::in(array_keys(config('appearance.neutrals')))],
        ];
        foreach (['light', 'dark'] as $mode) {
            $rules['palette.'.$mode] = ['required', 'array:'.implode(',', self::COLOR_FIELDS)];
            foreach (self::COLOR_FIELDS as $field) {
                $rules['palette.'.$mode.'.'.$field] = ['required', 'string', 'regex:/\A#[0-9a-fA-F]{6}\z/'];
            }
        }

        return $rules;
    }

    /** @param array<string, mixed> $palette */
    public function validateContrast(array $palette): void
    {
        $errors = [];
        foreach (['light', 'dark'] as $mode) {
            $colors = $palette[$mode];
            $neutral = config('appearance.neutrals.'.$palette['neutral']);
            $linkBackgrounds = [
                $colors['background'], $colors['surface'],
                $this->mix($colors['surface'], $colors['primary'], .08),
                $this->mix($colors['surface'], $neutral, $mode === 'dark' ? .2 : .08),
            ];
            if ($this->readable($colors['primary'], $linkBackgrounds) === null) {
                $errors['palette.'.$mode.'.surface'] = __('settings::appearance.contrast_surfaces');
            }
            foreach (['text', 'muted'] as $field) {
                foreach (['background', 'surface'] as $background) {
                    if ($this->contrast($colors[$field], $colors[$background]) < 4.5) {
                        $errors['palette.'.$mode.'.'.$field] = __('settings::appearance.contrast_text');
                    }
                }
            }
            foreach (['topbar', 'header', 'footer'] as $region) {
                if ($this->contrast($colors[$region.'_text'], $colors[$region]) < 4.5) {
                    $errors['palette.'.$mode.'.'.$region.'_text'] = __('settings::appearance.contrast_text');
                }
            }
            foreach (['background', 'surface'] as $background) {
                if ($this->contrast($colors['border'], $colors[$background]) < 3) {
                    $errors['palette.'.$mode.'.border'] = __('settings::appearance.contrast_control');
                }
            }
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /** @param array<string, mixed> $palette */
    public function save(array $palette, int $revision): void
    {
        $palette = Validator::make(['palette' => $palette, 'revision' => $revision], $this->rules())->validate()['palette'];
        $this->validateContrast($palette);

        DB::transaction(function () use ($palette, $revision): void {
            $this->initialize();
            $setting = Setting::where('group', 'appearance')->where('key', 'palette')->lockForUpdate()->firstOrFail();
            $stored = $setting->value;
            $currentRevision = (int) (is_array($stored) ? ($stored['revision'] ?? 0) : 0);

            if ($revision !== $currentRevision) {
                throw ValidationException::withMessages(['revision' => __('settings::appearance.stale')]);
            }

            $setting->update([
                'type' => 'json',
                'value' => json_encode($this->normalize($palette) + ['revision' => $revision + 1], JSON_THROW_ON_ERROR),
            ]);
            DB::afterCommit(fn () => app(SettingService::class)->clearCache());
        });
    }

    /** @return array<string, mixed> */
    public function normalize(mixed $stored): array
    {
        if (! is_array($stored)) {
            return $this->defaults();
        }
        unset($stored['revision']);
        foreach (['light', 'dark'] as $mode) {
            if (isset($stored[$mode]) && is_array($stored[$mode])) {
                $stored[$mode] += array_intersect_key(config('appearance.modes.'.$mode), array_flip(['topbar', 'topbar_text']));
            }
        }
        if (Validator::make(['palette' => $stored, 'revision' => 0], $this->rules())->fails()) {
            return $this->defaults();
        }
        try {
            $this->validateContrast($stored);
        } catch (ValidationException) {
            return $this->defaults();
        }
        foreach (['light', 'dark'] as $mode) {
            foreach (self::COLOR_FIELDS as $field) {
                $stored[$mode][$field] = strtoupper($stored[$mode][$field]);
            }
        }

        return $stored;
    }

    /** @param array<string, mixed> $palette
     * @return array<string, array<string, string>>
     */
    public function variables(array $palette): array
    {
        $palette = $this->normalize($palette);
        $result = [];
        foreach (['light', 'dark'] as $mode) {
            $colors = $palette[$mode];
            $variables = [];
            foreach ($colors as $name => $hex) {
                $variables['--site-'.str_replace('_', '-', $name)] = $hex;
            }
            foreach (['primary', 'accent'] as $brand) {
                $onColor = $this->foreground($colors[$brand]);
                $hover = $this->mix($colors[$brand], $onColor, 0.12);
                $variables['--site-on-'.$brand] = $onColor;
                $variables['--site-'.$brand.'-hover'] = $this->contrast($onColor, $hover) >= 4.5 ? $hover : $colors[$brand];
                foreach ([50 => .95, 100 => .9, 200 => .75, 300 => .6, 400 => .4, 500 => .2, 600 => 0, 700 => -.15, 800 => -.3, 900 => -.5, 950 => -.7] as $shade => $amount) {
                    $variables['--site-'.$brand.'-'.$shade] = $this->mix($colors[$brand], $amount >= 0 ? '#FFFFFF' : '#000000', abs($amount));
                }
            }
            $neutral = config('appearance.neutrals.'.$palette['neutral']);
            $variables['--site-surface-muted'] = $this->mix($colors['surface'], $neutral, $mode === 'dark' ? .2 : .08);
            $variables['--site-primary-soft'] = $this->mix($colors['surface'], $colors['primary'], .08);
            $variables['--site-divider'] = $this->mix($colors['surface'], $colors['border'], .25);
            $variables['--site-link'] = $this->readable($colors['primary'], [$colors['surface'], $colors['background'], $variables['--site-primary-soft'], $variables['--site-surface-muted']]);
            $variables['--site-focus'] = $this->readable($colors['primary'], [$colors['surface'], $colors['background']], 3);
            $variables['--site-strong'] = $colors['text'];
            $variables['--site-on-strong'] = $this->foreground($colors['text']);
            foreach (['topbar', 'header', 'footer'] as $region) {
                $variables['--site-'.$region.'-link'] = $this->readable($colors['primary'], [$colors[$region]]);
                $variables['--site-'.$region.'-muted'] = $this->readable($this->mix($colors[$region], $colors[$region.'_text'], .8), [$colors[$region]]);
            }
            $result[$mode] = $variables;
        }

        return $result;
    }

    public function stylesheet(): string
    {
        $variables = $this->variables($this->normalize(setting('appearance.palette')));
        $css = '';
        foreach (['light' => ':root', 'dark' => ':root.dark, :root.dark-theme'] as $mode => $selector) {
            $css .= $selector.'{';
            foreach ($variables[$mode] as $name => $value) {
                $css .= $name.':'.$value.';';
            }
            $css .= '}';
        }

        return $css;
    }

    public function contrast(string $first, string $second): float
    {
        $a = $this->luminance($first);
        $b = $this->luminance($second);

        return (max($a, $b) + .05) / (min($a, $b) + .05);
    }

    private function foreground(string $background): string
    {
        return $this->contrast('#FFFFFF', $background) >= $this->contrast('#000000', $background) ? '#FFFFFF' : '#000000';
    }

    /** @param list<string> $backgrounds */
    private function readable(string $color, array $backgrounds, float $minimum = 4.5): ?string
    {
        foreach (range(0, 100) as $step) {
            foreach (['#000000', '#FFFFFF'] as $target) {
                $candidate = $this->mix($color, $target, $step / 100);
                if (array_all($backgrounds, fn (string $background): bool => $this->contrast($candidate, $background) >= $minimum)) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    private function mix(string $first, string $second, float $amount): string
    {
        $rgb = [];
        foreach ([1, 3, 5] as $offset) {
            $rgb[] = (int) round(hexdec(substr($first, $offset, 2)) * (1 - $amount) + hexdec(substr($second, $offset, 2)) * $amount);
        }

        return sprintf('#%02X%02X%02X', ...$rgb);
    }

    private function luminance(string $hex): float
    {
        $rgb = [];
        foreach ([1, 3, 5] as $offset) {
            $channel = hexdec(substr($hex, $offset, 2)) / 255;
            $rgb[] = $channel <= .04045 ? $channel / 12.92 : (($channel + .055) / 1.055) ** 2.4;
        }

        return .2126 * $rgb[0] + .7152 * $rgb[1] + .0722 * $rgb[2];
    }
}
