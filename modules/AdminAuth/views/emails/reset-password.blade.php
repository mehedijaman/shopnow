@component('mail::message')
# {{ __('admin-auth::mail.reset.heading') }}

{{ __('admin-auth::mail.reset.intro') }}

@component('mail::button', ['url' => $url])
{{ __('admin-auth::mail.reset.button') }}
@endcomponent

{{ __('admin-auth::mail.reset.thanks') }}<br>
{{ config('app.name') }}
@endcomponent