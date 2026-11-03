<?php

namespace Modules\Settings\Services;

use Xenon\LaravelBDSms\Request;

trait SecureSmsProvider
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, string>  $headers
     */
    protected function makeRequest(string $url, array $query = [], array $headers = []): Request
    {
        return new SecureSmsRequest($url, $query, false, array_merge($this->senderObject->getHeaders(), $headers));
    }
}
