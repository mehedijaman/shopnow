<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | The single source of truth for every locale the application can serve.
    | Nothing outside this array is ever accepted as a locale: request input
    | is validated against these keys before it reaches App::setLocale() or
    | any translation/lang path.
    |
    | - name / native: label used by the language switchers.
    | - dir:           emitted on <html dir="...">.
    | - og_locale:     Open Graph locale.
    | - native_digits: render numbers with Bangla digits (display only).
    |
    */

    'supported' => [
        'en' => [
            'name' => 'En',
            'native' => 'En',
            'dir' => 'ltr',
            'og_locale' => 'en_US',
            'native_digits' => false,
        ],
        'bn' => [
            'name' => 'Bn',
            'native' => 'Bn',
            'dir' => 'ltr',
            'og_locale' => 'bn_BD',
            'native_digits' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Cookie
    |--------------------------------------------------------------------------
    |
    | The explicit language choice made through the switcher. The cookie is
    | shared between the storefront and the admin panel, but it is only ever
    | consulted after the authenticated account of the current guard.
    |
    */

    'cookie' => [
        'name' => 'locale',
        'lifetime' => 31536000, // 1 year, in minutes
    ],

];
