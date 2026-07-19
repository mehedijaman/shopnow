@component('mail::message')
# Your Downloads Are Ready

Thank you for your purchase! Your downloadable files are now available.

@foreach ($permissions as $permission)
**{{ $permission->productFile->name }}**

@php
$maxText = $permission->download_limit ? 'max. ' . $permission->download_limit . ' download(s)' : 'unlimited downloads';
$expiryText = $permission->expires_at ? 'Expires: ' . $permission->expires_at->format('d M Y') : 'No expiry';
@endphp

@component('mail::button', ['url' => url('/download/' . $permission->download_token)])
Download Now
@endcomponent

*{{ $maxText }} &middot; {{ $expiryText }}*

@endforeach

Thanks,<br>
{{ setting('branding.site_name', config('app.name')) }}
@endcomponent
