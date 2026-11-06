<?php

namespace Modules\Courier\Services\Steadfast;

use CourierHub\Drivers\Steadfast\SteadfastClient as BaseSteadfastClient;
use CourierHub\Exceptions\CourierApiException;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Throwable;

class SteadfastClient extends BaseSteadfastClient
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');
        $curlError = null;
        $requestWasSent = false;

        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
        ])
            ->acceptJson()
            ->timeout($this->timeout)
            ->connectTimeout(min(10, $this->timeout))
            ->withOptions([
                'on_stats' => function (TransferStats $stats) use (&$curlError, &$requestWasSent): void {
                    $curlError = $stats->getHandlerErrorData();
                    $handlerStats = $stats->getHandlerStats();
                    $requestWasSent = $requestWasSent || $stats->hasResponse()
                        || ($handlerStats['request_size'] ?? 0) > 0
                        || ($handlerStats['redirect_count'] ?? 0) > 0;
                },
            ])
            ->retry(2, 0, function (Throwable $exception, PendingRequest $request) use (&$curlError, &$requestWasSent, $url): bool {
                if (! $exception instanceof ConnectionException || $curlError !== CURLE_COULDNT_RESOLVE_HOST || $requestWasSent) {
                    return false;
                }

                $host = parse_url($url, PHP_URL_HOST);

                if (! is_string($host) || $host === '') {
                    return false;
                }

                $addresses = $this->resolveHost($host);

                if ($addresses === []) {
                    return false;
                }

                $port = parse_url($url, PHP_URL_PORT) ?? (parse_url($url, PHP_URL_SCHEME) === 'https' ? 443 : 80);
                $resolvePrefix = version_compare(curl_version()['version'], '7.75.0', '>=') ? '+' : '';

                $request->withOptions([
                    'curl' => [
                        CURLOPT_RESOLVE => [$resolvePrefix.$host.':'.$port.':'.implode(',', $addresses)],
                    ],
                ]);

                return true;
            }, throw: false)
            ->{$method}($url, $data);

        if ($response->failed()) {
            throw new CourierApiException(
                'Steadfast API Error: '.$response->body(),
                $response->status(),
                null,
                $response->json() ?? [],
            );
        }

        return $response->json();
    }

    /**
     * @return list<string>
     */
    protected function resolveHost(string $host): array
    {
        return gethostbynamel($host) ?: [];
    }
}
