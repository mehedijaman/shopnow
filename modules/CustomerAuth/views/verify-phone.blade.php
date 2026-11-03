@extends('site-layout')

@section('seo_title', __('customer-auth::otp.title'))

@section('content')
<div class="flex min-h-[75vh] items-center justify-center bg-gray-50 px-4 py-12 dark:bg-gray-900">
    <div class="w-full max-w-md space-y-6 rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700 sm:p-10">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('customer-auth::otp.title') }}</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('customer-auth::otp.intro', ['phone' => $phone]) }}</p>
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
            <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('customer-auth::otp.code') }}</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" required autofocus
                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-center text-2xl tracking-widest text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3 font-semibold text-white hover:bg-primary-700">{{ __('customer-auth::otp.verify') }}</button>
        </form>
        @if ($resendAt && $resendAt->isFuture())
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('customer-auth::otp.resend_at', ['time' => $resendAt->format('H:i:s')]) }}</p>
        @endif
        <form method="POST" action="{{ route('customerAuth.phone.resend') }}">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('customer-auth::otp.resend') }}</button>
        </form>
        <details class="text-sm text-gray-700 dark:text-gray-200">
            <summary class="cursor-pointer font-medium">{{ __('customer-auth::otp.change_phone') }}</summary>
            <form method="POST" action="{{ route('customerAuth.phone.change') }}" class="mt-4 space-y-3">
                @csrf
                <label for="phone" class="block">{{ __('customer-auth::otp.phone') }}</label>
                <input id="phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}" class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 font-semibold text-white">{{ __('customer-auth::otp.change_phone') }}</button>
            </form>
        </details>
        <form method="POST" action="{{ route('customerAuth.phone.cancel') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-primary-600 dark:text-primary-400">{{ __('customer-auth::otp.cancel') }}</button>
        </form>
    </div>
</div>
@endsection
