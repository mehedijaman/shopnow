@component('mail::message')
# {{ __('customer-auth::mail.reset.heading') }}

{{ __('customer-auth::mail.reset.intro') }}

@component('mail::button', ['url' => $url])
{{ __('customer-auth::mail.reset.button') }}
@endcomponent

{{ __('customer-auth::mail.reset.thanks') }}<br>
{{ config('app.name') }}
@endcomponent
