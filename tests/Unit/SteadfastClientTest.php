<?php

use CourierHub\Exceptions\CourierApiException;
use GuzzleHttp\Psr7\Response as PsrResponse;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Modules\Courier\Services\Steadfast\SteadfastClient;
use Modules\Courier\Services\Steadfast\SteadfastDriver;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Http::preventStrayRequests();

    $this->config = [
        'api_key' => 'test-key',
        'secret_key' => 'test-secret',
        'base_url' => 'https://portal.packzy.test/api/v1/',
    ];

    $this->client = Mockery::mock(SteadfastClient::class, [$this->config, ['timeout' => 8, 'retry' => 3]])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
});

test('retries only a DNS failure using system resolved addresses while preserving the URL and request', function () {
    $this->client->shouldReceive('resolveHost')->once()->with('portal.packzy.test')
        ->andReturn(['192.0.2.10', '192.0.2.11']);
    $attempts = [];

    Http::fake(function (Request $request, array $options) use (&$attempts) {
        $attempts[] = $options;

        expect($request->url())->toBe('https://portal.packzy.test/api/v1/create_order')
            ->and($request->method())->toBe('POST')
            ->and($request['invoice'])->toBe('ORDER-1')
            ->and($request->hasHeader('Api-Key', 'test-key'))->toBeTrue()
            ->and($request->hasHeader('Secret-Key', 'test-secret'))->toBeTrue()
            ->and($options['verify'])->toBeTrue();

        if (count($attempts) === 1) {
            expect($options['curl'][CURLOPT_RESOLVE] ?? null)->toBeNull();
            $options['on_stats'](new TransferStats($request->toPsrRequest(), null, 0, CURLE_COULDNT_RESOLVE_HOST));

            return Http::failedConnection()($request);
        }

        expect($options['curl'][CURLOPT_RESOLVE])->toBe([
            '+portal.packzy.test:443:192.0.2.10,192.0.2.11',
        ]);

        return Http::response(['consignment' => ['tracking_code' => 'STF-123']]);
    });

    $response = $this->client->post('/create_order', ['invoice' => 'ORDER-1']);

    expect($response['consignment']['tracking_code'])->toBe('STF-123')
        ->and($attempts)->toHaveCount(2);
});

test('does not resolve or retry when the request succeeds', function () {
    $this->client->shouldNotReceive('resolveHost');
    Http::fake(['*' => Http::response(['current_balance' => 100])]);

    expect($this->client->get('/get_balance'))->toBe(['current_balance' => 100]);
    Http::assertSentCount(1);
});

test('does not retry HTTP failures', function (int $status) {
    $this->client->shouldNotReceive('resolveHost');
    Http::fake(['*' => Http::response(['message' => 'Rejected'], $status)]);

    expect(fn () => $this->client->post('/create_order', ['invoice' => 'ORDER-1']))
        ->toThrow(CourierApiException::class);
    Http::assertSentCount(1);
})->with([401, 403, 429, 500]);

test('does not retry other connection failures', function (int $error) {
    $this->client->shouldNotReceive('resolveHost');
    $attempts = 0;

    Http::fake(function (Request $request, array $options) use (&$attempts, $error) {
        $attempts++;
        $options['on_stats'](new TransferStats($request->toPsrRequest(), null, 0, $error));

        return Http::failedConnection('cURL error '.$error)($request);
    });

    expect(fn () => $this->client->post('/create_order', ['invoice' => 'ORDER-1']))
        ->toThrow(ConnectionException::class);
    expect($attempts)->toBe(1);
})->with([
    'connection refused' => [CURLE_COULDNT_CONNECT],
    'timeout' => [CURLE_OPERATION_TIMEDOUT],
    'certificate verification failed' => [60],
]);

test('preserves the original error when system DNS also fails', function () {
    $this->client->shouldReceive('resolveHost')->once()->andReturn([]);
    $attempts = 0;

    Http::fake(function (Request $request, array $options) use (&$attempts) {
        $attempts++;
        $options['on_stats'](new TransferStats($request->toPsrRequest(), null, 0, CURLE_COULDNT_RESOLVE_HOST));

        return Http::failedConnection('cURL error 6: Could not resolve host: portal.packzy.test')($request);
    });

    expect(fn () => $this->client->post('/create_order', []))->toThrow(ConnectionException::class, 'Could not resolve host');
    expect($attempts)->toBe(1);
});

test('does not repeat a booking when DNS fails after a request or redirect', function (array $handlerStats, bool $redirectResponse) {
    $this->client->shouldNotReceive('resolveHost');
    $attempts = 0;

    Http::fake(function (Request $request, array $options) use (&$attempts, $handlerStats, $redirectResponse) {
        $attempts++;

        if ($redirectResponse) {
            $options['on_stats'](new TransferStats($request->toPsrRequest(), new PsrResponse(307)));
        }

        $options['on_stats'](new TransferStats($request->toPsrRequest(), null, 0, CURLE_COULDNT_RESOLVE_HOST, $handlerStats));

        return Http::failedConnection()($request);
    });

    expect(fn () => $this->client->post('/create_order', ['invoice' => 'ORDER-1']))
        ->toThrow(ConnectionException::class);
    expect($attempts)->toBe(1);
})->with([
    'redirect followed' => [['redirect_count' => 1, 'request_size' => 0], false],
    'request already sent' => [['redirect_count' => 0, 'request_size' => 200], false],
    'Guzzle redirect middleware received a response' => [[], true],
]);

test('stops after one fallback attempt if DNS keeps failing', function () {
    $this->client->shouldReceive('resolveHost')->once()->andReturn(['192.0.2.10']);
    $attempts = 0;

    Http::fake(function (Request $request, array $options) use (&$attempts) {
        $attempts++;
        $options['on_stats'](new TransferStats($request->toPsrRequest(), null, 0, CURLE_COULDNT_RESOLVE_HOST));

        return Http::failedConnection()($request);
    });

    expect(fn () => $this->client->post('/create_order', []))->toThrow(ConnectionException::class);
    expect($attempts)->toBe(2);
});

test('the module driver uses the DNS aware client for inherited endpoints', function () {
    $attempts = 0;
    Http::fake(function (Request $request, array $options) use (&$attempts) {
        $attempts++;

        expect($options)->toHaveKey('on_stats')
            ->and($options['connect_timeout'])->toBe(8);

        return Http::response(['balance' => 75]);
    });

    $driver = new SteadfastDriver($this->config, ['timeout' => 8]);

    expect($driver->getBalance()->current_balance)->toBe(75.0)
        ->and($attempts)->toBe(1);
});
