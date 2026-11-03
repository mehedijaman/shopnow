<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Order\Enums\OrderStatus;
use Modules\Settings\Models\Setting;
use Xenon\LaravelBDSms\Provider\Adn;
use Xenon\LaravelBDSms\Provider\AjuraTech;
use Xenon\LaravelBDSms\Provider\Alpha;
use Xenon\LaravelBDSms\Provider\Automas;
use Xenon\LaravelBDSms\Provider\Banglalink;
use Xenon\LaravelBDSms\Provider\BDBulkSms;
use Xenon\LaravelBDSms\Provider\BoomCast;
use Xenon\LaravelBDSms\Provider\Brilliant;
use Xenon\LaravelBDSms\Provider\BulkSmsBD;
use Xenon\LaravelBDSms\Provider\BulkSmsDhaka;
use Xenon\LaravelBDSms\Provider\CustomGateway;
use Xenon\LaravelBDSms\Provider\DhorolaSms;
use Xenon\LaravelBDSms\Provider\DianaHost;
use Xenon\LaravelBDSms\Provider\DianaSms;
use Xenon\LaravelBDSms\Provider\DnsBd;
use Xenon\LaravelBDSms\Provider\DurjoySoft;
use Xenon\LaravelBDSms\Provider\EAmarseba;
use Xenon\LaravelBDSms\Provider\ElitBuzz;
use Xenon\LaravelBDSms\Provider\Esms;
use Xenon\LaravelBDSms\Provider\Grameenphone;
use Xenon\LaravelBDSms\Provider\GreenWeb;
use Xenon\LaravelBDSms\Provider\Infobip;
use Xenon\LaravelBDSms\Provider\Khudebarta;
use Xenon\LaravelBDSms\Provider\Lpeek;
use Xenon\LaravelBDSms\Provider\MDL;
use Xenon\LaravelBDSms\Provider\Metronet;
use Xenon\LaravelBDSms\Provider\MimSms;
use Xenon\LaravelBDSms\Provider\Mobireach;
use Xenon\LaravelBDSms\Provider\Mobishasra;
use Xenon\LaravelBDSms\Provider\Muthofun;
use Xenon\LaravelBDSms\Provider\NovocomBd;
use Xenon\LaravelBDSms\Provider\Onnorokom;
use Xenon\LaravelBDSms\Provider\QuickSms;
use Xenon\LaravelBDSms\Provider\RedmoItSms;
use Xenon\LaravelBDSms\Provider\RhSmsBd;
use Xenon\LaravelBDSms\Provider\Robi;
use Xenon\LaravelBDSms\Provider\SendMySms;
use Xenon\LaravelBDSms\Provider\SmartLabSms;
use Xenon\LaravelBDSms\Provider\Sms4BD;
use Xenon\LaravelBDSms\Provider\SmsBangladesh;
use Xenon\LaravelBDSms\Provider\SmsinBD;
use Xenon\LaravelBDSms\Provider\SmsNet24;
use Xenon\LaravelBDSms\Provider\SmsNetBD;
use Xenon\LaravelBDSms\Provider\SMSNoc;
use Xenon\LaravelBDSms\Provider\SmsQ;
use Xenon\LaravelBDSms\Provider\SongBird;
use Xenon\LaravelBDSms\Provider\Ssl;
use Xenon\LaravelBDSms\Provider\Tense;
use Xenon\LaravelBDSms\Provider\TmssIct;
use Xenon\LaravelBDSms\Provider\TruboSms;
use Xenon\LaravelBDSms\Provider\Twenty4BulkSms;
use Xenon\LaravelBDSms\Provider\TwentyFourBulkSmsBD;
use Xenon\LaravelBDSms\Provider\Viatech;
use Xenon\LaravelBDSms\Provider\WinText;
use Xenon\LaravelBDSms\Provider\ZamanIt;
use Xenon\LaravelBDSms\Provider\ZendSms;

class SmsSettings
{
    public const PROVIDERS = [
        'twenty4bulksms' => [
            'name' => '24 Bulk SMS',
            'class' => Twenty4BulkSms::class,
            'fields' => ['api_key', 'sender_id', 'user_email'],
            'hosts' => ['24bulksms.com'],
        ],
        'twentyfourbulksmsbd' => [
            'name' => '24 Bulk SMS BD',
            'class' => TwentyFourBulkSmsBD::class,
            'fields' => ['customer_id', 'api_key'],
            'hosts' => ['www.24bulksmsbd.com'],
        ],
        'adn' => [
            'name' => 'ADN SMS',
            'class' => Adn::class,
            'fields' => ['api_key', 'api_secret', 'request_type', 'message_type'],
            'optional_fields' => ['senderid'],
            'defaults' => ['request_type' => 'SINGLE_SMS', 'message_type' => 'TEXT'],
            'hosts' => ['portal.adnsms.com'],
        ],
        'ajuratech' => [
            'name' => 'AjuraTech / Reve SMS',
            'class' => AjuraTech::class,
            'fields' => ['apikey', 'secretkey', 'callerID'],
            'hosts' => ['smpp.revesms.com'],
            'ports' => [7790],
        ],
        'alpha' => [
            'name' => 'Alpha SMS',
            'class' => Alpha::class,
            'fields' => ['api_key'],
            'optional_fields' => ['sender_id'],
            'hosts' => ['api.sms.net.bd'],
        ],
        'automas' => [
            'name' => 'Automas',
            'class' => Automas::class,
            'fields' => ['api_key', 'senderid'],
            'optional_fields' => ['type'],
            'defaults' => ['type' => 'text'],
            'hosts' => ['api.automas.com.bd'],
        ],
        'banglalink' => [
            'name' => 'Banglalink',
            'class' => Banglalink::class,
            'fields' => ['userID', 'passwd', 'sender'],
            'hosts' => ['vas.banglalink.net'],
        ],
        'bdbulksms' => [
            'name' => 'BD Bulk SMS',
            'class' => BDBulkSms::class,
            'fields' => ['token'],
            'hosts' => ['api.greenweb.com.bd'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'boomcast' => [
            'name' => 'BoomCast',
            'class' => BoomCast::class,
            'fields' => ['masking', 'username', 'password'],
            'hosts' => ['api.boom-cast.com'],
        ],
        'brilliant' => [
            'name' => 'Brilliant',
            'class' => Brilliant::class,
            'fields' => ['ApiKey', 'ClientId', 'SenderId'],
            'hosts' => ['sms.brilliant.com.bd'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'bulksmsdhaka' => [
            'name' => 'Bulk SMS Dhaka',
            'class' => BulkSmsDhaka::class,
            'fields' => ['api_key', 'callerID'],
            'hosts' => ['bulksmsdhaka.net'],
        ],
        'bulksmsbd' => [
            'name' => 'BulkSMSBD',
            'class' => BulkSmsBD::class,
            'fields' => ['api_key', 'senderid'],
            'hosts' => ['bulksmsbd.net'],
        ],
        'customgateway' => [
            'name' => 'Custom Gateway',
            'class' => CustomGateway::class,
            'fields' => ['url', 'method', 'payload', 'headers'],
            'unavailable_reason' => 'provider_custom_mapping',
        ],
        'dhorolasms' => [
            'name' => 'DhorolaSms',
            'class' => DhorolaSms::class,
            'fields' => ['apikey', 'sender'],
            'hosts' => ['api.dhorolasms.net'],
        ],
        'dianasms' => [
            'name' => 'Diana SMS',
            'class' => DianaSms::class,
            'fields' => ['ApiKey', 'ClientId', 'SenderId'],
            'hosts' => ['q.dianasms.com'],
        ],
        'dianahost' => [
            'name' => 'DianaHost',
            'class' => DianaHost::class,
            'fields' => ['api_key', 'type', 'senderid'],
            'hosts' => ['esms.dianahost.com'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'dnsbd' => [
            'name' => 'DNS BD',
            'class' => DnsBd::class,
            'unavailable_reason' => 'provider_not_implemented',
        ],
        'durjoysoft' => [
            'name' => 'DurjoySoft',
            'class' => DurjoySoft::class,
            'fields' => ['ApiKey', 'SenderID'],
            'hosts' => ['smsp.durjoysoft.com'],
        ],
        'eamarseba' => [
            'name' => 'E-Amarseba',
            'class' => EAmarseba::class,
            'fields' => ['x-app-key', 'x-app-secret'],
            'optional_fields' => ['is_masking', 'masking_name'],
            'defaults' => ['is_masking' => '0'],
            'hosts' => ['e-amarseba.com'],
        ],
        'elitbuzz' => [
            'name' => 'ElitBuzz',
            'class' => ElitBuzz::class,
            'fields' => ['url', 'api_key', 'senderid', 'type'],
            'defaults' => ['type' => 'text'],
            'hosts' => ['elitbuzz-bd.com'],
            'subdomains' => true,
        ],
        'esms' => [
            'name' => 'eSMS',
            'class' => Esms::class,
            'fields' => ['api_token', 'sender_id'],
            'hosts' => ['login.esms.com.bd'],
        ],
        'grameenphone' => [
            'name' => 'Grameenphone',
            'class' => Grameenphone::class,
            'fields' => ['username', 'password', 'messagetype'],
            'defaults' => ['messagetype' => '1'],
            'hosts' => ['gpcmp.grameenphone.com'],
        ],
        'greenweb' => [
            'name' => 'GreenWeb',
            'class' => GreenWeb::class,
            'fields' => ['token'],
            'hosts' => ['api.greenweb.com.bd'],
        ],
        'infobip' => [
            'name' => 'Infobip',
            'class' => Infobip::class,
            'fields' => ['base_url', 'from', 'user', 'password'],
            'hosts' => ['api.infobip.com'],
            'subdomains' => true,
        ],
        'khudebarta' => [
            'name' => 'Khudebarta / SMS.to',
            'class' => Khudebarta::class,
            'fields' => ['api_key', 'sender_id'],
            'optional_fields' => ['bypass_optout', 'callback_url'],
            'defaults' => ['bypass_optout' => '1'],
            'hosts' => ['api.sms.to'],
        ],
        'lpeek' => [
            'name' => 'Lpeek',
            'class' => Lpeek::class,
            'fields' => ['acode', 'apiKey', 'masking'],
            'optional_fields' => ['is_unicode', 'transactionType'],
            'defaults' => ['is_unicode' => '0', 'transactionType' => 'T'],
            'hosts' => ['sms.lpeek.com'],
        ],
        'mdl' => [
            'name' => 'MDL SMS',
            'class' => MDL::class,
            'fields' => ['api_key', 'type', 'senderid'],
            'hosts' => ['premium.mdlsms.com'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'metronet' => [
            'name' => 'Metronet',
            'class' => Metronet::class,
            'fields' => ['api_key', 'mask'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'mimsms' => [
            'name' => 'MIM SMS',
            'class' => MimSms::class,
            'fields' => ['ApiKey', 'SenderName', 'UserName'],
            'optional_fields' => ['TransactionType', 'CampaignId', 'CampaignName'],
            'defaults' => ['TransactionType' => 'T'],
            'hosts' => ['api.mimsms.com'],
        ],
        'mobireach' => [
            'name' => 'MobiReach',
            'class' => Mobireach::class,
            'fields' => ['Username', 'Password', 'From'],
            'hosts' => ['api.mobireach.com.bd'],
        ],
        'mobishasra' => [
            'name' => 'MobiShastra',
            'class' => Mobishasra::class,
            'fields' => ['user', 'pwd', 'senderid'],
            'hosts' => ['mshastra.com'],
        ],
        'muthofun' => [
            'name' => 'Muthofun',
            'class' => Muthofun::class,
            'fields' => ['sender_id', 'api_key'],
            'hosts' => ['sysadmin.muthobarta.com'],
        ],
        'novocombd' => [
            'name' => 'Novocom BD',
            'class' => NovocomBd::class,
            'fields' => ['ApiKey', 'ClientId', 'SenderId'],
            'hosts' => ['sms.novocom-bd.com'],
        ],
        'onnorokom' => [
            'name' => 'OnnoRokom SMS',
            'class' => Onnorokom::class,
            'fields' => ['userName', 'userPassword', 'type', 'maskName', 'campaignName'],
            'hosts' => ['api2.onnorokomsms.com'],
            'unavailable_reason' => 'provider_soap',
        ],
        'quicksms' => [
            'name' => 'Quick SMS',
            'class' => QuickSms::class,
            'fields' => ['api_key', 'senderid'],
            'optional_fields' => ['type'],
            'defaults' => ['type' => 'text'],
            'hosts' => ['server1.quicksms.xyz'],
        ],
        'redmoitsms' => [
            'name' => 'RedmoIT SMS',
            'class' => RedmoItSms::class,
            'fields' => ['api_token', 'sender_id'],
            'hosts' => ['sms.redmoit.com'],
            'unavailable_reason' => 'provider_closed',
        ],
        'rhsmsbd' => [
            'name' => 'RH SMS BD',
            'class' => RhSmsBd::class,
            'fields' => ['api_token'],
            'hosts' => ['rhsmsbd.top'],
        ],
        'robi' => [
            'name' => 'Robi',
            'class' => Robi::class,
            'fields' => ['username', 'password'],
            'hosts' => ['bmpws.robi.com.bd'],
        ],
        'sendmysms' => [
            'name' => 'SendMySMS',
            'class' => SendMySms::class,
            'fields' => ['user', 'key'],
            'hosts' => ['sendmysms.net'],
        ],
        'smartlabsms' => [
            'name' => 'SmartLab SMS',
            'class' => SmartLabSms::class,
            'fields' => ['user', 'password', 'sender'],
            'hosts' => ['labapi.smartlabsms.com'],
        ],
        'smsbangladesh' => [
            'name' => 'SMS Bangladesh',
            'class' => SmsBangladesh::class,
            'fields' => ['user', 'password', 'from'],
            'hosts' => ['panel.smsbangladesh.com'],
        ],
        'smsnetbd' => [
            'name' => 'SMS.net.bd',
            'class' => SmsNetBD::class,
            'fields' => ['api_key'],
            'optional_fields' => ['sender_id'],
            'hosts' => ['api.sms.net.bd'],
        ],
        'sms4bd' => [
            'name' => 'SMS4BD',
            'class' => Sms4BD::class,
            'fields' => ['publickey', 'privatekey', 'type', 'sender', 'delay'],
            'hosts' => ['www.sms4bd.net'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'smsinbd' => [
            'name' => 'SMSinBD',
            'class' => SmsinBD::class,
            'fields' => ['api_token', 'senderid'],
            'hosts' => ['api.smsinbd.com'],
        ],
        'smsnet24' => [
            'name' => 'SMSNet24',
            'class' => SmsNet24::class,
            'fields' => ['user_id', 'user_password'],
            'optional_fields' => ['route_id', 'sms_type_id'],
            'hosts' => ['sms.apinet.club'],
        ],
        'smsnoc' => [
            'name' => 'SMSNOC',
            'class' => SMSNoc::class,
            'fields' => ['sender_id', 'bearer_token'],
            'hosts' => ['smsnoc.com'],
        ],
        'smsq' => [
            'name' => 'SMSQ',
            'class' => SmsQ::class,
            'fields' => ['sender_id', 'api_key', 'client_id'],
            'hosts' => ['api.smsq.global'],
        ],
        'songbird' => [
            'name' => 'Songbird',
            'class' => SongBird::class,
            'fields' => ['apikey', 'secretkey', 'callerID'],
            'hosts' => ['103.53.84.15'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'ssl' => [
            'name' => 'SSL Wireless',
            'class' => Ssl::class,
            'fields' => ['api_token', 'sid'],
            'optional_fields' => ['secret_key'],
            'hosts' => ['smsplus.sslwireless.com'],
        ],
        'tense' => [
            'name' => 'Tense',
            'class' => Tense::class,
            'fields' => ['user', 'password', 'campaign', 'masking'],
            'hosts' => ['sms.tense.com.bd'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'tmssict' => [
            'name' => 'TMSS ICT',
            'class' => TmssIct::class,
            'fields' => ['api_key'],
            'optional_fields' => ['request_type', 'message_type', 'campaign_title', 'isPromotional'],
            'defaults' => ['request_type' => 'SINGLE_SMS', 'message_type' => 'TEXT', 'campaign_title' => 'ShopNow'],
            'hosts' => ['sms.tmssict.com'],
        ],
        'trubosms' => [
            'name' => 'TruboSMS',
            'class' => TruboSms::class,
            'fields' => ['api_token', 'sender_id'],
            'hosts' => ['panel.trubosms.com'],
        ],
        'viatech' => [
            'name' => 'Viatech',
            'class' => Viatech::class,
            'fields' => ['api_key', 'mask'],
            'hosts' => ['masking.viatech.com.bd'],
            'unavailable_reason' => 'provider_http_only',
        ],
        'wintext' => [
            'name' => 'WinText',
            'class' => WinText::class,
            'fields' => ['token'],
            'optional_fields' => ['messagetype', 'ismasking', 'masking'],
            'defaults' => ['messagetype' => '1', 'ismasking' => 'false'],
            'hosts' => ['api.wintextbd.com'],
        ],
        'zamanit' => [
            'name' => 'Zaman IT',
            'class' => ZamanIt::class,
            'fields' => ['api_key', 'type', 'senderid'],
            'hosts' => ['sms.zaman-it.com'],
        ],
        'zendsms' => [
            'name' => 'ZendSMS',
            'class' => ZendSms::class,
            'fields' => ['api_key', 'sender_id'],
            'hosts' => ['api.zendsms.com'],
        ],
    ];

    /** @return array<string, array<string, mixed>> */
    public function providerOptions(): array
    {
        return array_map(static fn (array $definition): array => array_intersect_key($definition, array_flip([
            'name', 'fields', 'optional_fields', 'defaults', 'unavailable_reason',
        ])), self::PROVIDERS);
    }

    /** @return list<string> */
    public static function credentialFields(string $provider): array
    {
        $definition = self::PROVIDERS[$provider] ?? [];

        return array_merge($definition['fields'] ?? [], $definition['optional_fields'] ?? []);
    }

    public static function canSend(string $provider): bool
    {
        return isset(self::PROVIDERS[$provider]) && ! isset(self::PROVIDERS[$provider]['unavailable_reason']);
    }

    public static function allowsEndpoint(string $url, ?string $provider = null): bool
    {
        $parts = parse_url($url);
        if (! is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }
        $host = strtolower($parts['host'] ?? '');
        $port = $parts['port'] ?? 443;
        $definitions = $provider === null ? self::PROVIDERS : [$provider => self::PROVIDERS[$provider] ?? []];
        foreach ($definitions as $key => $definition) {
            if (! self::canSend($key) || ! in_array($port, $definition['ports'] ?? [443], true)) {
                continue;
            }
            foreach ($definition['hosts'] ?? [] as $allowedHost) {
                if ($host === $allowedHost || (! empty($definition['subdomains']) && str_ends_with($host, '.'.$allowedHost))) {
                    return true;
                }
            }
        }

        return false;
    }

    /** @return array<string, mixed> */
    public function defaults(): array
    {
        $defaults = [
            'enabled' => false, 'provider' => 'ssl', 'order_placed' => false,
            'order_updated' => false, 'statuses' => ['processing', 'shipped', 'delivered', 'completed', 'cancelled'],
            'otp_enabled' => false, 'otp_expiry_minutes' => 5, 'otp_cooldown_seconds' => 60,
            'otp_max_attempts' => 5,
        ];
        foreach (['en', 'bn'] as $locale) {
            $defaults['placement_'.$locale] = $locale === 'en'
                ? '{site_name}: Thank you {customer_name}. Order #{order_number} received. Total BDT {total}.'
                : '{site_name}: ধন্যবাদ {customer_name}। অর্ডার #{order_number} গ্রহণ করা হয়েছে। মোট {total} টাকা।';
            $defaults['otp_'.$locale] = $locale === 'en'
                ? '{site_name}: Your verification code is {otp}. Valid for {minutes} minutes.'
                : '{site_name}: আপনার যাচাইকরণ কোড {otp}। মেয়াদ {minutes} মিনিট।';
            foreach (OrderStatus::values() as $status) {
                $defaults['status_'.$status.'_'.$locale] = $locale === 'en'
                    ? '{site_name}: Order #{order_number} is now {status}.'
                    : '{site_name}: অর্ডার #{order_number} এর অবস্থা: {status}।';
            }
        }

        return $defaults;
    }

    /** @return array<string, mixed> */
    public function values(): array
    {
        $stored = Setting::query()->where('group', 'sms')->where('key', '!=', 'credentials')->get()
            ->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->value])->all();
        $values = array_replace($this->defaults(), $stored);
        foreach ($this->defaults() as $key => $default) {
            if (is_int($default)) {
                $values[$key] = (int) $values[$key];
            }
        }

        return $values;
    }

    /** @return array<string, string> */
    public function credentials(string $provider): array
    {
        $encrypted = Setting::query()->where('group', 'sms')->where('key', 'credentials')->value('value');
        $credentials = $encrypted ? json_decode(Crypt::decryptString($encrypted), true, 512, JSON_THROW_ON_ERROR) : [];

        return array_replace(self::PROVIDERS[$provider]['defaults'] ?? [], $credentials[$provider] ?? []);
    }

    /** @return array<string, mixed> */
    public function forForm(): array
    {
        return array_merge($this->values(), ['credentials' => [], 'remove_credentials' => false]);
    }

    /** @return array<string, array<string, bool>> */
    public function configured(): array
    {
        $result = [];
        $encrypted = Setting::query()->where('group', 'sms')->where('key', 'credentials')->value('value');
        $all = $encrypted ? json_decode(Crypt::decryptString($encrypted), true, 512, JSON_THROW_ON_ERROR) : [];
        foreach (self::PROVIDERS as $provider => $definition) {
            $credentials = array_replace($definition['defaults'] ?? [], $all[$provider] ?? []);
            foreach (self::credentialFields($provider) as $field) {
                $result[$provider][$field] = filled($credentials[$field] ?? null);
            }
        }

        return $result;
    }

    /** @param array<string, mixed> $data */
    public function save(array $data): void
    {
        DB::transaction(function () use ($data): void {
            $provider = $data['provider'];
            if (! isset(self::PROVIDERS[$provider]) || (($data['enabled'] || $data['otp_enabled']) && ! self::canSend($provider))) {
                throw ValidationException::withMessages(['provider' => __('settings::sms.provider_unavailable')]);
            }
            $row = Setting::query()->firstOrCreate(['group' => 'sms', 'key' => 'credentials'], [
                'type' => 'text', 'value' => null, 'is_public' => false,
            ]);
            $row = Setting::query()->lockForUpdate()->findOrFail($row->id);
            $all = $row->value ? json_decode(Crypt::decryptString($row->value), true, 512, JSON_THROW_ON_ERROR) : [];
            $credentials = array_replace(self::PROVIDERS[$provider]['defaults'] ?? [], ($data['remove_credentials'] ?? false) ? [] : ($all[$provider] ?? []));
            foreach (self::credentialFields($provider) as $field) {
                if (filled($data['credentials'][$field] ?? null)) {
                    $credentials[$field] = $data['credentials'][$field];
                }
            }
            foreach (['url', 'base_url'] as $field) {
                if (filled($credentials[$field] ?? null) && ! self::allowsEndpoint($credentials[$field], $provider)) {
                    throw ValidationException::withMessages(['credentials.'.$field => __('settings::sms.invalid_provider_url')]);
                }
            }
            if ($data['enabled'] || $data['otp_enabled']) {
                foreach (self::PROVIDERS[$provider]['fields'] as $field) {
                    if (blank($credentials[$field] ?? null)) {
                        throw ValidationException::withMessages(['credentials.'.$field => __('settings::sms.credential_required')]);
                    }
                }
            }
            $all[$provider] = $credentials;
            $row->update(['value' => Crypt::encryptString(json_encode($all, JSON_THROW_ON_ERROR))]);
            $defaults = $this->defaults();
            foreach (array_keys($defaults) as $key) {
                if (! array_key_exists($key, $data)) {
                    continue;
                }
                $value = $data[$key];
                $type = is_bool($defaults[$key]) ? 'boolean' : (is_array($value) ? 'json' : 'text');
                Setting::query()->updateOrCreate(['group' => 'sms', 'key' => $key], [
                    'value' => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : $value,
                    'type' => $type, 'is_public' => false,
                ]);
            }
        });
    }

    public static function phone(string $phone): ?string
    {
        $digits = preg_replace('/[\s()+-]/', '', $phone);
        if (str_starts_with($digits, '880')) {
            $digits = substr($digits, 2);
        }

        return preg_match('/^01[3-9][0-9]{8}$/D', $digits) ? $digits : null;
    }

    /** @param array<string, string|int> $values */
    public function render(string $template, array $values): string
    {
        $values['site_name'] = (string) setting('branding.site_name', config('app.name'));
        $replacements = [];
        foreach ($values as $key => $value) {
            $replacements['{'.$key.'}'] = (string) $value;
        }

        return strtr($template, $replacements);
    }
}
