@component('mail::message')
# {{ __('product::mail.download_ready.heading') }}

{{ __('product::mail.download_ready.intro') }}

@foreach ($permissions as $permission)
**{{ $permission->productFile->name }}**

@php
$maxText = $permission->download_limit
    ? __('product::mail.download_ready.max_downloads', ['count' => $permission->download_limit])
    : __('product::mail.download_ready.unlimited');
$expiryText = $permission->expires_at
    ? __('product::mail.download_ready.expires', ['date' => $permission->expires_at->format('d M Y')])
    : __('product::mail.download_ready.no_expiry');
@endphp

@component('mail::button', ['url' => url('/download/' . $permission->download_token)])
{{ __('product::mail.download_ready.button') }}
@endcomponent

*{{ $maxText }} &middot; {{ $expiryText }}*

@endforeach

{{ __('product::mail.download_ready.thanks') }}<br>
{{ setting('branding.site_name', config('app.name')) }}
@endcomponent
