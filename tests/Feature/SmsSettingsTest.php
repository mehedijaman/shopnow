<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Settings\Models\Setting;
use Modules\Settings\Models\SmsMessage;
use Modules\Settings\Services\SettingService;
use Modules\Settings\Services\SmsSettings;
use Modules\User\Models\User;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Queue::fake();
    $this->admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'root', 'guard_name' => 'user']);
    $this->admin->assignRole('root');
    $this->smsData = array_merge(app(SmsSettings::class)->defaults(), [
        'enabled' => true, 'provider' => 'bulksmsbd',
        'credentials' => ['api_key' => 'sms-super-secret', 'senderid' => 'SHOP'],
    ]);
});

test('sms settings and endpoints require permissions', function () {
    $this->get('/admin/settings/sms')->assertRedirect();
    $user = User::factory()->create();
    $this->actingAs($user, 'user')->get('/admin/settings/sms')->assertForbidden();
    $this->post('/admin/settings/sms', $this->smsData)->assertForbidden();
    $this->postJson('/admin/settings/sms/test', ['phone' => '01712345678', 'message' => 'Test'])->assertForbidden();
    $this->getJson('/admin/settings/sms/activity')->assertForbidden();
});

test('sms settings encrypt secrets and never expose them through props cache or audit logs', function () {
    $this->actingAs($this->admin, 'user')->post('/admin/settings/sms', $this->smsData)->assertSessionHasNoErrors()->assertRedirect();
    $raw = DB::table('settings')->where('group', 'sms')->where('key', 'credentials')->value('value');
    expect($raw)->not->toContain('sms-super-secret');
    expect(app(SmsSettings::class)->credentials('bulksmsbd')['api_key'])->toBe('sms-super-secret');
    expect(app(SettingService::class)->getAll())->not->toHaveKey('sms.credentials');
    expect(Activity::all()->toJson())->not->toContain('sms-super-secret');
    expect(Setting::where('group', 'sms')->where('is_public', true)->exists())->toBeFalse();
    $this->get('/admin/settings/sms')->assertInertia(fn (Assert $page) => $page
        ->where('group', 'sms')->where('settings.credentials', [])->where('sms.configured.bulksmsbd.api_key', true));
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['credentials' => []]))->assertSessionHasNoErrors();
    expect(app(SmsSettings::class)->credentials('bulksmsbd')['api_key'])->toBe('sms-super-secret');
});

test('sms settings validate provider credentials templates and otp dependencies', function () {
    $this->actingAs($this->admin, 'user');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['credentials' => []]))->assertSessionHasErrors('credentials.api_key');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['provider' => 'arbitrary-class']))->assertSessionHasErrors('provider');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['enabled' => false, 'otp_enabled' => true]))->assertSessionHasErrors('enabled');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['otp_en' => 'No code here']))->assertSessionHasErrors('otp_en');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['placement_en' => '{password}']))->assertSessionHasErrors('placement_en');
    expect(session('_old_input.credentials'))->toBeNull();
});

test('test sms uses saved credentials even when automation is disabled and hides recipient data', function () {
    app(SmsSettings::class)->save(array_merge($this->smsData, ['enabled' => false]));
    $this->actingAs($this->admin, 'user')->postJson('/admin/settings/sms/test', [
        'phone' => '+8801712345678', 'message' => 'Test message',
    ])->assertAccepted()->assertJsonPath('attempt.recipient', '017*****678')->assertJsonPath('attempt.status', 'pending');
    $message = SmsMessage::first();
    expect($message->phone)->toBe('01712345678')->and($message->created_by)->toBe($this->admin->id);
    expect($message->getRawOriginal('body'))->not->toContain('Test message');
    $this->getJson('/admin/settings/sms/activity')->assertOk()->assertDontSee('01712345678')->assertDontSee('Test message');
});

test('test sms is rate limited', function () {
    app(SmsSettings::class)->save($this->smsData);
    $this->actingAs($this->admin, 'user');
    for ($i = 0; $i < 3; $i++) {
        $this->postJson('/admin/settings/sms/test', ['phone' => '01712345678', 'message' => 'Test'])->assertAccepted();
    }
    $this->postJson('/admin/settings/sms/test', ['phone' => '01712345678', 'message' => 'Test'])->assertTooManyRequests();
});

test('sms provider dropdown includes every installed package provider with its credential definitions', function () {
    $classes = [];
    foreach (glob(base_path('vendor/xenon/laravelbdsms/src/Provider/*.php')) as $file) {
        $name = basename($file, '.php');
        if (! in_array($name, ['AbstractProvider', 'ProviderRoadmap'], true)) {
            $classes[] = 'Xenon\\LaravelBDSms\\Provider\\'.$name;
        }
    }
    expect(array_column(SmsSettings::PROVIDERS, 'class'))->toEqualCanonicalizing($classes);
    $this->actingAs($this->admin, 'user')->get('/admin/settings/sms')->assertInertia(fn (Assert $page) => $page
        ->has('sms.providers', count($classes))
        ->where('sms.providers.adn.fields', ['api_key', 'api_secret', 'request_type', 'message_type'])
        ->where('sms.providers.dnsbd.unavailable_reason', 'provider_not_implemented')
        ->missing('sms.providers.adn.class')
    );
});

test('new provider credentials preserve casing encryption and gateway defaults', function (string $provider, array $credentials) {
    $data = array_merge($this->smsData, ['provider' => $provider, 'credentials' => $credentials]);
    $this->actingAs($this->admin, 'user')->post('/admin/settings/sms', $data)->assertSessionHasNoErrors();
    $stored = app(SmsSettings::class)->credentials($provider);
    expect($stored)->toMatchArray($credentials);
    foreach (SmsSettings::PROVIDERS[$provider]['defaults'] ?? [] as $key => $value) {
        expect($stored[$key])->toBe($value);
    }
    $this->postJson('/admin/settings/sms/test', ['phone' => '01712345678', 'message' => 'Test'])->assertAccepted();
})->with([
    ['adn', ['api_key' => 'secret', 'api_secret' => 'secret']],
    ['mimsms', ['ApiKey' => 'secret', 'SenderName' => 'SHOP', 'UserName' => 'test-user']],
    ['eamarseba', ['x-app-key' => 'secret', 'x-app-secret' => 'secret']],
    ['twentyfourbulksmsbd', ['customer_id' => 'test-customer', 'api_key' => 'secret']],
    ['tmssict', ['api_key' => 'secret']],
]);

test('provider configuration rejects unrelated fields and unsafe endpoints', function () {
    $this->actingAs($this->admin, 'user');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['credentials' => ['api_key' => 'secret', 'senderid' => 'SHOP', 'password' => 'unrelated']]))
        ->assertSessionHasErrors('credentials');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['provider' => 'infobip', 'credentials' => [
        'base_url' => 'https://127.0.0.1', 'user' => 'user', 'password' => 'secret', 'from' => 'SHOP',
    ]]))->assertSessionHasErrors('credentials.base_url');
    $this->post('/admin/settings/sms', array_merge($this->smsData, ['provider' => 'dnsbd', 'credentials' => []]))->assertSessionHasErrors('provider');
});
