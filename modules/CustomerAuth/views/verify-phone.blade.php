@extends('site-layout')

@section('seo_title', __('customer-auth::otp.title'))

@section('content')
<div class="flex min-h-[75vh] items-center justify-center bg-site-surface-muted px-4 py-12 dark:bg-site-surface-muted">
    <div class="w-full max-w-md space-y-6 rounded-2xl bg-site-surface p-6 shadow-xl ring-1 ring-site-border dark:bg-site-surface-muted dark:ring-site-border sm:p-10">
        <h1 class="text-2xl font-bold text-site-text dark:text-site-text">{{ __('customer-auth::otp.title') }}</h1>
        <p class="text-sm text-site-muted dark:text-site-text">{{ __('customer-auth::otp.intro', ['phone' => $phone]) }}</p>
        @if (session('success'))
            <p role="status" class="rounded-lg bg-green-50 p-3 text-sm text-green-800 dark:bg-green-900 dark:text-green-100">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900 dark:text-red-100">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('customerAuth.phone.verify') }}" class="space-y-4">
            @csrf
            <label for="code" class="block text-sm font-semibold text-site-text dark:text-site-text">{{ __('customer-auth::otp.code') }}</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" required autofocus
                class="block w-full rounded-xl border border-site-border bg-site-surface px-4 py-3 text-center text-2xl tracking-widest text-site-text focus:border-site-focus focus:ring-site-focus dark:border-site-border dark:bg-site-surface-muted dark:text-site-text">
            <button type="submit" class="w-full rounded-xl bg-site-primary px-4 py-3 font-semibold text-site-on-primary hover:bg-site-primary-hover">{{ __('customer-auth::otp.verify') }}</button>
        </form>
        @if ($resendAt && $resendAt->isFuture())
            <p class="text-xs text-site-muted dark:text-site-muted">{{ __('customer-auth::otp.resend_at', ['time' => $resendAt->format('H:i:s')]) }}</p>
        @endif
        <form method="POST" action="{{ route('customerAuth.phone.resend') }}">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-site-divider px-4 py-2 text-sm font-medium text-site-text hover:bg-site-surface-muted dark:border-site-divider dark:text-site-text dark:hover:bg-site-surface-muted">{{ __('customer-auth::otp.resend') }}</button>
        </form>
        <details class="text-sm text-site-text dark:text-site-text">
            <summary class="cursor-pointer font-medium">{{ __('customer-auth::otp.change_phone') }}</summary>
            <form method="POST" action="{{ route('customerAuth.phone.change') }}" class="mt-4 space-y-3">
                @csrf
                <label for="phone" class="block">{{ __('customer-auth::otp.phone') }}</label>
                <input id="phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}" class="block w-full rounded-lg border border-site-border bg-site-surface px-3 py-2 text-site-text dark:border-site-border dark:bg-site-surface-muted dark:text-site-text">
                <button type="submit" class="rounded-lg bg-site-primary px-4 py-2 font-semibold text-site-on-primary">{{ __('customer-auth::otp.change_phone') }}</button>
            </form>
        </details>
        <form method="POST" action="{{ route('customerAuth.phone.cancel') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-site-link dark:text-site-link">{{ __('customer-auth::otp.cancel') }}</button>
        </form>
    </div>
</div>
@endsection
