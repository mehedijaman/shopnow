<?php

use App\Jobs\SendSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\PhoneVerification;
use Modules\CustomerAuth\Services\RegistrationOtp;
use Modules\Settings\Models\SmsMessage;
use Modules\Settings\Services\SmsGateway;
use Modules\Settings\Services\SmsSettings;

beforeEach(function () {
    Queue::fake();
    app(SmsSettings::class)->save(array_merge(app(SmsSettings::class)->defaults(), [
        'enabled' => true, 'otp_enabled' => true, 'provider' => 'bulksmsbd',
        'credentials' => ['api_key' => 'test-key', 'senderid' => 'SHOP'],
    ]));
    $this->signupData = [
        'name' => 'New Customer', 'email' => 'new@example.com', 'phone' => '+8801712345678',
        'password' => 'secret-password', 'confirm_password' => 'secret-password',
    ];
});

function registrationSmsCode(): string
{
    preg_match('/\b([0-9]{6})\b/', SmsMessage::query()->where('purpose', 'otp')->latest('id')->first()->body, $matches);

    return $matches[1];
}

test('registration remains unauthenticated until a one-time code verifies the phone', function () {
    $this->post('/signup', $this->signupData)->assertRedirect(route('customerAuth.phone.show'));
    $customer = Customer::where('email', 'new@example.com')->firstOrFail();
    $this->assertGuest('customer');
    expect($customer->phone)->toBe('01712345678')->and($customer->requiresPhoneVerification())->toBeTrue();
    $code = registrationSmsCode();
    expect(DB::table('sms_messages')->first()->body)->not->toContain($code);
    expect(PhoneVerification::first()->digest)->not->toBe($code);
    $this->get('/verify-phone')->assertOk()->assertSee('017*****678')->assertDontSee($code);
    $this->post('/verify-phone', ['code' => $code])->assertRedirect('/');
    $this->assertAuthenticatedAs($customer, 'customer');
    expect($customer->fresh()->phone_verified_at)->not->toBeNull()->and(PhoneVerification::first()->consumed_at)->not->toBeNull();
    expect(app(RegistrationOtp::class)->verify($customer, $code))->toBeFalse();
});

test('wrong codes lock a challenge and expired codes cannot authenticate', function () {
    $this->post('/signup', $this->signupData);
    $code = registrationSmsCode();
    $wrong = $code === '000000' ? '111111' : '000000';
    for ($i = 0; $i < 5; $i++) {
        $this->post('/verify-phone', ['code' => $wrong])->assertSessionHasErrors('code');
    }
    $this->post('/verify-phone', ['code' => $code])->assertSessionHasErrors('code');
    $this->assertGuest('customer');
    expect(PhoneVerification::first()->attempts)->toBe(5);
    $this->travel(6)->minutes();
    expect(app(RegistrationOtp::class)->verify(Customer::first(), $code))->toBeFalse();
});

test('resend respects cooldown invalidates the old code and cancels superseded work', function () {
    $this->post('/signup', $this->signupData);
    $oldCode = registrationSmsCode();
    $old = SmsMessage::first();
    $this->post('/verify-phone/resend')->assertSessionHasErrors('code');
    $this->travel(61)->seconds();
    $this->post('/verify-phone/resend')->assertSessionHasNoErrors()->assertRedirect();
    expect($old->fresh()->status)->toBe('cancelled')->and($old->fresh()->body)->toBeNull();
    $newCode = registrationSmsCode();
    $customer = Customer::first();
    if ($newCode !== $oldCode) {
        expect(app(RegistrationOtp::class)->verify($customer, $oldCode))->toBeFalse();
    }
    expect(app(RegistrationOtp::class)->verify($customer, $newCode))->toBeTrue();
});

test('pending accounts resume verification through password login and cannot bypass middleware', function () {
    $this->post('/signup', $this->signupData);
    $this->post('/verify-phone/cancel');
    $this->post('/login', ['login_identity' => '+8801712345678', 'password' => 'secret-password'])
        ->assertRedirect(route('customerAuth.phone.show'));
    $this->assertGuest('customer');
    $this->actingAs(Customer::first(), 'customer')->get('/account/profile')->assertRedirect(route('customerAuth.phone.show'));
    $this->assertGuest('customer');
});

test('existing customers keep access and otp-disabled registration retains its previous behavior', function () {
    $customer = Customer::factory()->create();
    $this->post('/login', ['login_identity' => $customer->email, 'password' => 'password'])->assertRedirect('/');
    $this->assertAuthenticatedAs($customer, 'customer');
    app(SmsSettings::class)->save(array_merge(app(SmsSettings::class)->values(), ['otp_enabled' => false]));
    $this->get('/logout');
    $this->post('/signup', $this->signupData)->assertRedirect('/');
    expect(Customer::where('email', 'new@example.com')->first()->requiresPhoneVerification())->toBeFalse();
    expect(SmsMessage::count())->toBe(0);
});

test('verified phone changes clear verification and require another challenge', function () {
    $customer = Customer::factory()->create(['phone_verified_at' => now()]);
    $customer->update(['phone' => '01812345678']);
    expect($customer->fresh()->phone_verified_at)->toBeNull()->and($customer->fresh()->requiresPhoneVerification())->toBeTrue();
});

test('expired and superseded otp messages never reach the gateway', function () {
    $this->post('/signup', $this->signupData);
    $message = SmsMessage::first();
    PhoneVerification::first()->update(['expires_at' => now()->subSecond()]);
    $gateway = Mockery::mock(SmsGateway::class);
    $gateway->shouldNotReceive('send');
    (new SendSms($message->id))->handle(app(SmsSettings::class), $gateway);
    expect($message->fresh()->status)->toBe('expired')->and($message->fresh()->body)->toBeNull();
});

test('pending registrations can correct a phone without bypassing cooldown or ownership checks', function () {
    $this->post('/signup', $this->signupData);
    $old = SmsMessage::first();
    $this->post('/verify-phone/change', ['phone' => '01812345678'])->assertSessionHasErrors('code');
    expect(Customer::first()->phone)->toBe('01712345678');
    $this->travel(61)->seconds();
    $this->post('/verify-phone/change', ['phone' => '+8801812345678'])->assertSessionHasNoErrors();
    expect(Customer::first()->phone)->toBe('01812345678')->and($old->fresh()->status)->toBe('cancelled');
    $code = registrationSmsCode();
    $this->post('/verify-phone', ['code' => $code])->assertRedirect('/');
    $this->assertAuthenticatedAs(Customer::first(), 'customer');
});

test('otp requirement persists across configuration changes and verification limits survive resend', function () {
    $this->post('/signup', $this->signupData);
    app(SmsSettings::class)->save(array_merge(app(SmsSettings::class)->values(), ['otp_enabled' => false]));
    $customer = Customer::first();
    expect($customer->requiresPhoneVerification())->toBeTrue();
    $code = registrationSmsCode();
    $wrong = $code === '000000' ? '111111' : '000000';
    for ($i = 0; $i < 5; $i++) {
        app(RegistrationOtp::class)->verify($customer, $wrong);
    }
    $this->travel(61)->seconds();
    app(RegistrationOtp::class)->issue($customer, '127.0.0.1');
    $code = registrationSmsCode();
    $wrong = $code === '000000' ? '111111' : '000000';
    for ($i = 0; $i < 5; $i++) {
        app(RegistrationOtp::class)->verify($customer, $wrong);
    }
    $this->travel(61)->seconds();
    expect(fn () => app(RegistrationOtp::class)->issue($customer, '127.0.0.1'))->toThrow(ValidationException::class);
});
