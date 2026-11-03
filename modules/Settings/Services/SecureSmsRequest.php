<?php

namespace Modules\Settings\Services;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Xenon\LaravelBDSms\Request;

class SecureSmsRequest extends Request
{
    /** @var array<string, mixed>|null */
    private ?array $secureFormParams = null;

    /** @param array<string, mixed> $form_params */
    public function setFormParams(array $form_params): Request
    {
        $this->secureFormParams = $form_params;

        return $this;
    }

    /** @param array<string, string> $headers */
    public function setHeaders(array $headers): Request
    {
        unset($headers['verify']);

        return parent::setHeaders(array_merge($this->getHeaders(), $headers));
    }

    public function get(bool $verify = false, float $timeout = 10.0): ResponseInterface
    {
        return $this->request('GET');
    }

    public function post(bool $verify = false, float $timeout = 20.0): ResponseInterface
    {
        return $this->request('POST');
    }

    private function request(string $method): ResponseInterface
    {
        $url = $this->getRequestUrl();
        if (! SmsSettings::allowsEndpoint($url)) {
            throw new \LogicException('Unsupported SMS endpoint.');
        }
        /** @var ClientInterface $client */
        $client = app('sms.http');
        $response = $client->request($method, $url, [
            'verify' => true, 'connect_timeout' => 3, 'timeout' => 15,
            'allow_redirects' => false, 'http_errors' => false,
            'headers' => $this->getHeaders(),
            $this->isContentTypeJson() ? 'json' : ($method === 'GET' ? 'query' : 'form_params') => $this->secureFormParams ?? $this->getQuery(),
        ]);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new \RuntimeException('Unexpected gateway HTTP status.');
        }

        return $response;
    }
}
