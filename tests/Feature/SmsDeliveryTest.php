<?php

use App\Jobs\SendSms;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Modules\Settings\Models\SmsMessage;
use Modules\Settings\Services\PackageSmsGateway;
use Modules\Settings\Services\SmsGateway;
use Modules\Settings\Services\SmsMessages;
use Modules\Settings\Services\SmsSettings;

beforeEach(function () {
    Queue::fake();
    app(SmsSettings::class)->save(array_merge(app(SmsSettings::class)->defaults(), [
        'enabled' => true, 'provider' => 'bulksmsbd', 'credentials' => ['api_key' => 'test-key', 'senderid' => 'SHOP'],
    ]));
});

test('package adapter handles gateway verdicts and enforces verified https', function (string $body, string $expected) {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([new Response(200, [], $body)]));
    $stack->push(Middleware::history($history));
    app()->instance('sms.http', new Client(['handler' => $stack]));
    $result = app(PackageSmsGateway::class)->send('bulksmsbd', ['api_key' => 'key', 'senderid' => 'SHOP'], '01712345678', 'hello', 'ref-1');
    expect($result['status'])->toBe($expected)
        ->and($history[0]['options']['verify'])->toBeTrue()
        ->and($history[0]['options']['allow_redirects'])->toBeFalse()
        ->and($history[0]['options']['connect_timeout'])->toBe(3)
        ->and($history[0]['options']['timeout'])->toBe(15);
})->with([
    ['{"response_code":202}', 'accepted'],
    ['{"response_code":1007,"error_message":"insufficient balance"}', 'rejected'],
    ['{"response_code":9999}', 'unknown'],
    ['malformed', 'unknown'],
]);

test('delivery claims each intent once and scrubs bodies after gateway acceptance', function () {
    $gateway = Mockery::mock(SmsGateway::class);
    $gateway->shouldReceive('send')->once()->andReturn(['status' => 'accepted', 'provider_id' => 'message-1', 'error' => null]);
    $message = app(SmsMessages::class)->create('test:once', 'test', '01712345678', 'Private test');
    (new SendSms($message->id))->handle(app(SmsSettings::class), $gateway);
    (new SendSms($message->id))->handle(app(SmsSettings::class), $gateway);
    expect($message->fresh()->status)->toBe('accepted')->and($message->fresh()->attempts)->toBe(1)
        ->and($message->fresh()->body)->toBeNull()->and($message->fresh()->provider_id)->toBe('message-1');
});

test('transport timeout remains unknown and is never blindly retried or logged with secrets', function () {
    $gateway = Mockery::mock(SmsGateway::class);
    $gateway->shouldReceive('send')->once()->andThrow(new ConnectException('secret-key and OTP 123456', new Request('POST', 'https://example.com')));
    $message = app(SmsMessages::class)->create('test:timeout', 'test', '01712345678', 'Private');
    (new SendSms($message->id))->handle(app(SmsSettings::class), $gateway);
    (new SendSms($message->id))->handle(app(SmsSettings::class), $gateway);
    expect($message->fresh()->status)->toBe('unknown')->and($message->fresh()->error)->toBe('transport_outcome_unknown')
        ->and($message->fresh()->body)->toBeNull();
});

test('rolled back transaction discards sms intent and does not dispatch', function () {
    try {
        DB::transaction(function () {
            app(SmsMessages::class)->create('test:rollback', 'test', '01712345678', 'No send');
            throw new RuntimeException('rollback');
        });
    } catch (RuntimeException) {
    }
    expect(SmsMessage::count())->toBe(0);
    Queue::assertNothingPushed();
});

test('disabled automation and expired work do not contact gateway', function () {
    $gateway = Mockery::mock(SmsGateway::class);
    $gateway->shouldNotReceive('send');
    $message = app(SmsMessages::class)->create('order:disabled', 'order_placed', '01712345678', 'Disabled');
    (new SendSms($message->id))->handle(app(SmsSettings::class), $gateway);
    expect($message->fresh()->status)->toBe('cancelled');
    $expired = app(SmsMessages::class)->create('test:expired', 'test', '01712345678', 'Expired', ['expires_at' => now()->subSecond()]);
    (new SendSms($expired->id))->handle(app(SmsSettings::class), $gateway);
    expect($expired->fresh()->status)->toBe('expired');
});

test('maintenance recovers pending jobs and marks interrupted sends unknown', function () {
    $pending = app(SmsMessages::class)->create('test:recover', 'test', '01712345678', 'Recover');
    $pending->update(['created_at' => now()->subMinutes(3)]);
    $interrupted = app(SmsMessages::class)->create('test:interrupted', 'test', '01712345678', 'Private');
    $interrupted->update(['status' => 'processing', 'started_at' => now()->subMinutes(3)]);
    $this->artisan('sms:maintain')->assertSuccessful();
    Queue::assertPushed(SendSms::class, fn (SendSms $job) => $job->messageId === $pending->id);
    expect($interrupted->fresh()->status)->toBe('unknown')->and($interrupted->fresh()->body)->toBeNull();
});

test('ssl adapter checks each recipient and signs encrypted payloads when configured', function () {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([new Response(200, [], json_encode([
        'status' => 'SUCCESS', 'smsinfo' => [['sms_status' => 'BLOCKED', 'reference_id' => 'ssl-ref']],
    ]))]));
    $stack->push(Middleware::history($history));
    app()->instance('sms.http', new Client(['handler' => $stack]));
    $result = app(PackageSmsGateway::class)->send('ssl', [
        'api_token' => 'ssl-token', 'sid' => 'SHOP', 'secret_key' => 'ssl-secret',
    ], '01712345678', 'Private OTP 123456', '12345678-abcd-abcd-abcd-123456789abc');
    expect($result['status'])->toBe('rejected')->and($result['provider_id'])->toBe('ssl-ref');
    $request = $history[0]['request'];
    $body = json_decode((string) $request->getBody(), true);
    expect(strlen($body['csms_id']))->toBe(20)->and($body['sms'])->not->toContain('123456');
    $decoded = base64_decode($body['sms']);
    $plaintext = openssl_decrypt(substr($decoded, 16), 'AES-256-CBC', substr(hash('sha256', 'ssl-secret'), 0, 32), 0, substr($decoded, 0, 16));
    expect($plaintext)->toBe('Private OTP 123456');
    $canonical = http_build_query(['csms_id' => $body['csms_id'], 'msisdn' => $body['msisdn'], 'sid' => $body['sid'], 'sms' => $body['sms']]);
    expect($request->getHeaderLine('X-Signature'))->toBe(hash_hmac('sha256', $canonical, 'ssl-secret'));
});

test('smsnetbd adapter interprets its documented response without exposing raw errors', function (string $body, string $status) {
    app()->instance('sms.http', new Client(['handler' => HandlerStack::create(new MockHandler([new Response(200, [], $body)]))]));
    $result = app(PackageSmsGateway::class)->send('smsnetbd', ['api_key' => 'key'], '01712345678', 'Hello', 'ref-1');
    expect($result['status'])->toBe($status);
})->with([
    ['{"error":0,"data":{"request_id":123}}', 'accepted'],
    ['{"error":417,"msg":"Insufficient balance"}', 'rejected'],
]);

test('every selectable package provider can construct a secure request with its own credentials', function (string $provider) {
    $definition = SmsSettings::PROVIDERS[$provider];
    $credentials = array_replace(array_fill_keys($definition['fields'], 'test-value'), $definition['defaults'] ?? []);
    if ($provider === 'infobip') {
        $credentials['base_url'] = 'https://example.api.infobip.com';
    }
    if ($provider === 'elitbuzz') {
        $credentials['url'] = 'https://msg.elitbuzz-bd.com';
    }
    $history = [];
    $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{}')]));
    $stack->push(Middleware::history($history));
    app()->instance('sms.http', new Client(['handler' => $stack]));
    $result = app(PackageSmsGateway::class)->send($provider, $credentials, '01712345678', 'Test message', '12345678-abcd-abcd-abcd-123456789abc');
    expect($result['status'])->toBeIn(['accepted', 'rejected', 'unknown'])
        ->and($history)->toHaveCount(1)
        ->and($history[0]['request']->getUri()->getScheme())->toBe('https')
        ->and($history[0]['options']['verify'])->toBeTrue()
        ->and($history[0]['options']['allow_redirects'])->toBeFalse();
})->with(fn () => array_map(static fn (string $key): array => [$key], array_keys(array_filter(
    SmsSettings::PROVIDERS,
    static fn (array $definition): bool => ! isset($definition['unavailable_reason']),
))));

test('expanded form providers retain all form fields in the actual outgoing payload', function () {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{}')]));
    $stack->push(Middleware::history($history));
    app()->instance('sms.http', new Client(['handler' => $stack]));
    app(PackageSmsGateway::class)->send('adn', ['api_key' => 'adn-key', 'api_secret' => 'adn-secret'], '01712345678', 'Hello ADN', 'ref-1');
    parse_str((string) $history[0]['request']->getBody(), $payload);
    expect($payload)->toMatchArray([
        'api_key' => 'adn-key', 'api_secret' => 'adn-secret', 'request_type' => 'SINGLE_SMS',
        'message_type' => 'TEXT', 'mobile' => '8801712345678', 'message_body' => 'Hello ADN',
    ]);
});

test('infobip receives one country prefix and restricts account endpoints', function () {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{}')]));
    $stack->push(Middleware::history($history));
    app()->instance('sms.http', new Client(['handler' => $stack]));
    $credentials = ['base_url' => 'https://example.api.infobip.com', 'user' => 'test-user', 'password' => 'test-password', 'from' => 'SHOP'];
    app(PackageSmsGateway::class)->send('infobip', $credentials, '01712345678', 'Hello', 'ref-1');
    $payload = json_decode((string) $history[0]['request']->getBody(), true);
    expect($payload['to'])->toBe('+8801712345678');
    $credentials['base_url'] = 'https://127.0.0.1';
    expect(fn () => app(PackageSmsGateway::class)->send('infobip', $credentials, '01712345678', 'Hello', 'ref-2'))->toThrow(LogicException::class);
    expect($history)->toHaveCount(1);
});
