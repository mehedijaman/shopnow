<?php

namespace Modules\Settings\Services;

use Xenon\LaravelBDSms\Provider\Adn;
use Xenon\LaravelBDSms\Provider\AjuraTech;
use Xenon\LaravelBDSms\Provider\Alpha;
use Xenon\LaravelBDSms\Provider\Automas;
use Xenon\LaravelBDSms\Provider\Banglalink;
use Xenon\LaravelBDSms\Provider\BoomCast;
use Xenon\LaravelBDSms\Provider\BulkSmsBD;
use Xenon\LaravelBDSms\Provider\BulkSmsDhaka;
use Xenon\LaravelBDSms\Provider\DhorolaSms;
use Xenon\LaravelBDSms\Provider\DianaSms;
use Xenon\LaravelBDSms\Provider\DurjoySoft;
use Xenon\LaravelBDSms\Provider\EAmarseba;
use Xenon\LaravelBDSms\Provider\ElitBuzz;
use Xenon\LaravelBDSms\Provider\Esms;
use Xenon\LaravelBDSms\Provider\Grameenphone;
use Xenon\LaravelBDSms\Provider\GreenWeb;
use Xenon\LaravelBDSms\Provider\Infobip;
use Xenon\LaravelBDSms\Provider\Khudebarta;
use Xenon\LaravelBDSms\Provider\Lpeek;
use Xenon\LaravelBDSms\Provider\MimSms;
use Xenon\LaravelBDSms\Provider\Mobireach;
use Xenon\LaravelBDSms\Provider\Mobishasra;
use Xenon\LaravelBDSms\Provider\Muthofun;
use Xenon\LaravelBDSms\Provider\NovocomBd;
use Xenon\LaravelBDSms\Provider\QuickSms;
use Xenon\LaravelBDSms\Provider\RhSmsBd;
use Xenon\LaravelBDSms\Provider\Robi;
use Xenon\LaravelBDSms\Provider\SendMySms;
use Xenon\LaravelBDSms\Provider\SmartLabSms;
use Xenon\LaravelBDSms\Provider\SmsBangladesh;
use Xenon\LaravelBDSms\Provider\SmsinBD;
use Xenon\LaravelBDSms\Provider\SmsNet24;
use Xenon\LaravelBDSms\Provider\SmsNetBD;
use Xenon\LaravelBDSms\Provider\SMSNoc;
use Xenon\LaravelBDSms\Provider\SmsQ;
use Xenon\LaravelBDSms\Provider\Ssl;
use Xenon\LaravelBDSms\Provider\TmssIct;
use Xenon\LaravelBDSms\Provider\TruboSms;
use Xenon\LaravelBDSms\Provider\Twenty4BulkSms;
use Xenon\LaravelBDSms\Provider\TwentyFourBulkSmsBD;
use Xenon\LaravelBDSms\Provider\WinText;
use Xenon\LaravelBDSms\Provider\ZamanIt;
use Xenon\LaravelBDSms\Provider\ZendSms;
use Xenon\LaravelBDSms\Sender;

class PackageSmsGateway implements SmsGateway
{
    public function send(string $provider, array $credentials, string $phone, string $message, string $reference): array
    {
        if (! SmsSettings::canSend($provider)) {
            throw new \LogicException('SMS provider is unavailable.');
        }
        $credentials = array_replace(SmsSettings::PROVIDERS[$provider]['defaults'] ?? [], $credentials);
        foreach (['url', 'base_url'] as $field) {
            if (filled($credentials[$field] ?? null) && ! SmsSettings::allowsEndpoint($credentials[$field], $provider)) {
                throw new \LogicException('Unsupported SMS provider endpoint.');
            }
        }
        $reference = substr(str_replace('-', '', $reference), 0, 20);
        $headers = [];
        if ($provider === 'ssl' && filled($credentials['secret_key'] ?? null)) {
            $secret = $credentials['secret_key'];
            $iv = random_bytes(16);
            $ciphertext = openssl_encrypt($message, 'AES-256-CBC', substr(hash('sha256', $secret), 0, 32), 0, $iv);
            if ($ciphertext === false) {
                throw new \RuntimeException('Unable to encrypt SSL Wireless message.');
            }
            $message = base64_encode($iv.$ciphertext);
            $canonical = http_build_query(['csms_id' => $reference, 'msisdn' => '88'.$phone, 'sid' => $credentials['sid'], 'sms' => $message]);
            $headers['X-Signature'] = hash_hmac('sha256', $canonical, $secret);
        }
        $sender = new Sender;
        $sender->setConfig(array_merge($credentials, ['csms_id' => $reference, 'requestID' => $reference, 'client_ref' => $reference]))
            ->setMobile($provider === 'infobip' ? $phone : '88'.$phone)->setMessage($message)->setHeaders($headers)->setQueue(false)->setAcceptance(null);
        $gateway = match ($provider) {
            'twenty4bulksms' => new class($sender) extends Twenty4BulkSms
            {
                use SecureSmsProvider;
            },
            'twentyfourbulksmsbd' => new class($sender) extends TwentyFourBulkSmsBD
            {
                use SecureSmsProvider;
            },
            'adn' => new class($sender) extends Adn
            {
                use SecureSmsProvider;
            },
            'ajuratech' => new class($sender) extends AjuraTech
            {
                use SecureSmsProvider;
            },
            'alpha' => new class($sender) extends Alpha
            {
                use SecureSmsProvider;
            },
            'automas' => new class($sender) extends Automas
            {
                use SecureSmsProvider;
            },
            'banglalink' => new class($sender) extends Banglalink
            {
                use SecureSmsProvider;
            },
            'boomcast' => new class($sender) extends BoomCast
            {
                use SecureSmsProvider;
            },
            'bulksmsdhaka' => new class($sender) extends BulkSmsDhaka
            {
                use SecureSmsProvider;
            },
            'bulksmsbd' => new class($sender) extends BulkSmsBD
            {
                use SecureSmsProvider;
            },
            'dhorolasms' => new class($sender) extends DhorolaSms
            {
                use SecureSmsProvider;
            },
            'dianasms' => new class($sender) extends DianaSms
            {
                use SecureSmsProvider;
            },
            'durjoysoft' => new class($sender) extends DurjoySoft
            {
                use SecureSmsProvider;
            },
            'eamarseba' => new class($sender) extends EAmarseba
            {
                use SecureSmsProvider;
            },
            'elitbuzz' => new class($sender) extends ElitBuzz
            {
                use SecureSmsProvider;
            },
            'esms' => new class($sender) extends Esms
            {
                use SecureSmsProvider;
            },
            'grameenphone' => new class($sender) extends Grameenphone
            {
                use SecureSmsProvider;
            },
            'greenweb' => new class($sender) extends GreenWeb
            {
                use SecureSmsProvider;
            },
            'infobip' => new class($sender) extends Infobip
            {
                use SecureSmsProvider;
            },
            'khudebarta' => new class($sender) extends Khudebarta
            {
                use SecureSmsProvider;
            },
            'lpeek' => new class($sender) extends Lpeek
            {
                use SecureSmsProvider;
            },
            'mimsms' => new class($sender) extends MimSms
            {
                use SecureSmsProvider;
            },
            'mobireach' => new class($sender) extends Mobireach
            {
                use SecureSmsProvider;
            },
            'mobishasra' => new class($sender) extends Mobishasra
            {
                use SecureSmsProvider;
            },
            'muthofun' => new class($sender) extends Muthofun
            {
                use SecureSmsProvider;
            },
            'novocombd' => new class($sender) extends NovocomBd
            {
                use SecureSmsProvider;
            },
            'quicksms' => new class($sender) extends QuickSms
            {
                use SecureSmsProvider;
            },
            'rhsmsbd' => new class($sender) extends RhSmsBd
            {
                use SecureSmsProvider;
            },
            'robi' => new class($sender) extends Robi
            {
                use SecureSmsProvider;
            },
            'sendmysms' => new class($sender) extends SendMySms
            {
                use SecureSmsProvider;
            },
            'smartlabsms' => new class($sender) extends SmartLabSms
            {
                use SecureSmsProvider;
            },
            'smsbangladesh' => new class($sender) extends SmsBangladesh
            {
                use SecureSmsProvider;
            },
            'smsnetbd' => new class($sender) extends SmsNetBD
            {
                use SecureSmsProvider;
            },
            'smsinbd' => new class($sender) extends SmsinBD
            {
                use SecureSmsProvider;
            },
            'smsnet24' => new class($sender) extends SmsNet24
            {
                use SecureSmsProvider;
            },
            'smsnoc' => new class($sender) extends SMSNoc
            {
                use SecureSmsProvider;
            },
            'smsq' => new class($sender) extends SmsQ
            {
                use SecureSmsProvider;
            },
            'ssl' => new class($sender) extends Ssl
            {
                use SecureSmsProvider;
            },
            'tmssict' => new class($sender) extends TmssIct
            {
                use SecureSmsProvider;
            },
            'trubosms' => new class($sender) extends TruboSms
            {
                use SecureSmsProvider;
            },
            'wintext' => new class($sender) extends WinText
            {
                use SecureSmsProvider;
            },
            'zamanit' => new class($sender) extends ZamanIt
            {
                use SecureSmsProvider;
            },
            'zendsms' => new class($sender) extends ZendSms
            {
                use SecureSmsProvider;
            },
            default => throw new \LogicException('Unsupported SMS provider.'),
        };
        $gateway->errorException();
        $report = json_decode($gateway->sendRequest(), true, 512, JSON_THROW_ON_ERROR);
        $payload = json_decode($report['response'] ?? '', true);
        $accepted = $sender->getAcceptance();
        $providerId = null;
        if (is_array($payload)) {
            if ($provider === 'ssl' && isset($payload['status'])) {
                $accepted = match (strtoupper((string) $payload['status'])) {
                    'SUCCESS' => true, 'FAILED', 'FAIL', 'ERROR' => false, default => null,
                };
                if ($accepted === true && isset($payload['smsinfo'][0]['sms_status'])) {
                    $accepted = match (strtoupper((string) $payload['smsinfo'][0]['sms_status'])) {
                        'SUCCESS' => true,
                        'INVALID', 'BLOCKED' => false,
                        default => null,
                    };
                }
                $providerId = $payload['smsinfo'][0]['reference_id'] ?? null;
            }
            if (in_array($provider, ['smsnetbd', 'alpha'], true) && isset($payload['error']) && is_numeric($payload['error'])) {
                $accepted = (int) $payload['error'] === 0;
            }
        }

        return [
            'status' => match ($accepted) {
                true => 'accepted', false => 'rejected', null => 'unknown'
            },
            'provider_id' => isset($providerId) ? substr((string) $providerId, 0, 100) : (isset($payload['data']['request_id']) ? substr((string) $payload['data']['request_id'], 0, 100) : null),
            'error' => $accepted === false ? 'gateway_rejected' : ($accepted === null ? 'unrecognized_response' : null),
        ];
    }
}
