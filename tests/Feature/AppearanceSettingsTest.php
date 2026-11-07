<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\AppearanceSettings;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->appearance = app(AppearanceSettings::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole(Role::findOrCreate('root', 'user'));
    $this->actingAs($this->admin, 'user');
    Cache::forget('settings');
});

afterEach(fn () => Cache::forget('settings'));

test('appearance page exposes only palette data and preset metadata', function () {
    $this->get('/admin/settings/appearance')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Settings/SettingsForm', false)
        ->where('group', 'appearance')
        ->has('settings.palette.light.primary')
        ->where('settings.revision', 0)
        ->has('appearance.presets.emerald')
        ->missing('settings.password'));
});

test('appearance view save and preview require their dedicated permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'user');

    $this->get('/admin/settings/appearance')->assertForbidden();
    $this->post('/admin/settings/appearance', [])->assertForbidden();
    $this->postJson('/admin/settings/appearance/preview', [])->assertForbidden();
    $this->get('/admin/settings/general')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->where('groups', fn ($groups) => ! in_array('appearance', $groups->all(), true)));

    $user->givePermissionTo('appearance-settings-edit');
    $this->get('/admin/settings/appearance')->assertOk();
});

test('saving publishes a normalized complete palette increments revision and invalidates cache', function () {
    $payload = $this->appearance->forForm();
    $payload['palette'] = $this->appearance->defaults('emerald');
    $payload['palette']['light']['primary'] = '#047857';
    setting('appearance.palette');
    $beforeLogs = DB::table('activity_log')->count();

    $this->post('/admin/settings/appearance', $payload)->assertRedirect()->assertSessionHasNoErrors();

    $saved = $this->appearance->forForm();
    expect($saved['revision'])->toBe(1)
        ->and($saved['palette']['preset'])->toBe('emerald')
        ->and(setting('appearance.palette')['light']['primary'])->toBe('#047857')
        ->and(DB::table('activity_log')->count())->toBeGreaterThan($beforeLogs);

    $this->appearance->initialize();
    expect($this->appearance->forForm())->toBe($saved);
});

test('stale tabs cannot overwrite a saved palette or reset it', function () {
    $stale = $this->appearance->forForm();
    $new = $stale;
    $new['palette'] = $this->appearance->defaults('rose');
    $this->post('/admin/settings/appearance', $new)->assertSessionHasNoErrors();
    $before = $this->appearance->forForm();
    $beforeLogs = DB::table('activity_log')->count();

    $this->post('/admin/settings/appearance', $stale)->assertSessionHasErrors('revision');

    expect($this->appearance->forForm())->toBe($before)
        ->and(DB::table('activity_log')->count())->toBe($beforeLogs);
});

test('defaults can be restored through the same revision checked save', function () {
    $payload = $this->appearance->forForm();
    $payload['palette'] = $this->appearance->defaults('violet');
    $this->post('/admin/settings/appearance', $payload)->assertSessionHasNoErrors();
    $this->post('/admin/settings/appearance', ['palette' => $this->appearance->defaults(), 'revision' => 1])->assertSessionHasNoErrors();

    expect($this->appearance->forForm())->toBe(['palette' => $this->appearance->defaults(), 'revision' => 2]);
});

test('invalid color formats and CSS fragments are rejected without changing settings', function (string $color) {
    $payload = $this->appearance->forForm();
    $payload['palette']['light']['primary'] = $color;

    $this->post('/admin/settings/appearance', $payload)->assertSessionHasErrors('palette.light.primary');
    expect($this->appearance->forForm()['revision'])->toBe(0);
})->with(['red', '#fff', '#11223344', 'var(--secret)', '</style><script>alert(1)</script>', "#123\n456"]);

test('unknown nested fields and unsupported versions are rejected', function () {
    $payload = $this->appearance->forForm();
    $payload['palette']['light']['custom_css'] = 'body { display:none; }';
    $this->post('/admin/settings/appearance', $payload)->assertSessionHasErrors('palette.light');
    unset($payload['palette']['light']['custom_css']);
    $payload['palette']['version'] = 999;
    $this->post('/admin/settings/appearance', $payload)->assertSessionHasErrors('palette.version');
});

test('unreadable text and control borders cannot be published', function () {
    $payload = $this->appearance->forForm();
    $payload['palette']['light']['text'] = '#FFFFFF';
    $payload['palette']['dark']['border'] = $payload['palette']['dark']['surface'];

    $this->post('/admin/settings/appearance', $payload)
        ->assertSessionHasErrors(['palette.light.text', 'palette.dark.border']);
    expect($this->appearance->forForm()['revision'])->toBe(0);
});

test('preview uses the live palette renderer without publishing or logging', function () {
    $payload = $this->appearance->forForm();
    $payload['palette'] = $this->appearance->defaults('warm');
    $logs = DB::table('activity_log')->count();
    $saved = $this->appearance->forForm();

    $this->postJson('/admin/settings/appearance/preview', $payload)->assertOk()
        ->assertJsonPath('variables.light.--site-primary', '#92400E')
        ->assertExactJson(['variables' => $this->appearance->variables($payload['palette'])]);

    expect($this->appearance->forForm())->toBe($saved)
        ->and(DB::table('activity_log')->count())->toBe($logs);
});

test('backgrounds that make generated links unreadable are rejected', function () {
    $payload = $this->appearance->forForm();
    $payload['palette']['light'] = array_merge($payload['palette']['light'], [
        'background' => '#FFFFFF', 'surface' => '#000000',
        'text' => '#767676', 'muted' => '#767676', 'border' => '#767676',
    ]);

    $this->post('/admin/settings/appearance', $payload)->assertSessionHasErrors('palette.light.surface');
    expect($this->appearance->forForm()['revision'])->toBe(0);
});

test('live stylesheet only contains safe public tokens and tolerates corrupted storage', function () {
    $palette = $this->appearance->defaults('emerald');
    $this->appearance->save($palette, 0);
    Cache::forget('settings');
    $css = $this->appearance->stylesheet();
    expect($css)->toContain('--site-primary:#047857;', ':root.dark, :root.dark-theme{')
        ->not->toContain('password', 'api_key', '<script');
    $this->view('site-layout', ['seo' => []])->assertSee('id="site-appearance"', false)->assertSee('--site-primary:#047857;', false);

    Setting::where('group', 'appearance')->where('key', 'palette')->update(['value' => '{"light":{"primary":"</style>"}}']);
    Cache::forget('settings');
    expect($this->appearance->stylesheet())->toContain('--site-primary:#2563EB;')->not->toContain('</style>');
});

test('a rolled back palette update does not publish changes', function () {
    $before = $this->appearance->forForm();
    DB::beginTransaction();
    $this->appearance->save($this->appearance->defaults('rose'), $before['revision']);
    DB::rollBack();

    expect($this->appearance->forForm())->toBe($before);
});

test('saved and cached palettes without topbar fields preserve existing colors and revision', function () {
    $palette = $this->appearance->defaults('emerald');
    foreach (['light', 'dark'] as $mode) {
        unset($palette[$mode]['topbar'], $palette[$mode]['topbar_text']);
    }
    $document = $palette + ['revision' => 7];
    $encoded = json_encode($document, JSON_THROW_ON_ERROR);
    Setting::where('group', 'appearance')->where('key', 'palette')->update(['value' => $encoded]);
    Cache::put('settings', ['appearance.palette' => $document]);
    $beforeLogs = DB::table('activity_log')->count();

    $form = $this->appearance->forForm();
    expect($form['palette'])->toEqual($this->appearance->defaults('emerald'))
        ->and($form['revision'])->toBe(7)
        ->and($this->appearance->stylesheet())->toContain('--site-primary:#047857;', '--site-topbar:#020617;')
        ->and(Setting::where('group', 'appearance')->where('key', 'palette')->first()->getRawOriginal('value'))->toBe($encoded)
        ->and(DB::table('activity_log')->count())->toBe($beforeLogs);
});

test('topbar colors can be previewed and published independently of header and footer', function () {
    $payload = $this->appearance->forForm();
    $payload['palette']['light']['topbar'] = '#FDE68A';
    $payload['palette']['light']['topbar_text'] = '#1F2937';
    $payload['palette']['dark']['topbar'] = '#FFFFFF';
    $payload['palette']['dark']['topbar_text'] = '#111827';

    $this->postJson('/admin/settings/appearance/preview', $payload)->assertOk()
        ->assertJsonPath('variables.light.--site-topbar', '#FDE68A')
        ->assertJsonPath('variables.light.--site-topbar-text', '#1F2937')
        ->assertJsonPath('variables.dark.--site-topbar', '#FFFFFF');
    $this->post('/admin/settings/appearance', $payload)->assertSessionHasNoErrors();
    expect($this->appearance->forForm()['palette'])->toBe($payload['palette']);

    Setting::updateOrCreate(['group' => 'contact', 'key' => 'email'], ['value' => '["hello@example.test"]', 'type' => 'repeater']);
    Cache::forget('settings');
    $this->get('/')->assertOk()->assertSee('class="site-topbar ', false)
        ->assertSee('--site-topbar:#FDE68A;', false)->assertSee('--site-topbar-text:#1F2937;', false);
});

test('topbar text cannot be published on an unreadable background', function (string $mode) {
    $payload = $this->appearance->forForm();
    $payload['palette'][$mode]['topbar_text'] = $payload['palette'][$mode]['topbar'];

    $this->post('/admin/settings/appearance', $payload)->assertSessionHasErrors('palette.'.$mode.'.topbar_text');
    $this->postJson('/admin/settings/appearance/preview', $payload)->assertUnprocessable()
        ->assertJsonValidationErrors('palette.'.$mode.'.topbar_text');
    expect($this->appearance->forForm()['revision'])->toBe(0);
})->with(['light', 'dark']);
