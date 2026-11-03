<?php

namespace Modules\Settings\Services;

interface SmsGateway
{
    /**
     * @param  array<string, string>  $credentials
     * @return array{status: string, provider_id: ?string, error: ?string}
     */
    public function send(string $provider, array $credentials, string $phone, string $message, string $reference): array;
}
