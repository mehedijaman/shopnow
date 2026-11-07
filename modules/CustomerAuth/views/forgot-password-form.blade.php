@extends('customer-auth::layouts.master')

@section('content')

<div class="flex items-center justify-center min-h-screen px-4 py-12 bg-site-surface-muted sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <div>
            <h2 class="mt-6 text-3xl font-extrabold leading-9 text-center text-site-text">
                Esqueceu sua senha?
            </h2>
        </div>

        @if (session('status'))
        <div class="px-4 py-2 mt-3 text-sm text-green-700 bg-site-surface-muted rounded-sm" role="alert">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <p class="mt-6 mb-2 text-center text-site-text">
            Ooops, algo deu errado
        </p>
        <ul>
            @foreach ($errors->all() as $error)
            <li class="px-4 py-2 text-sm text-white bg-red-700 rounded-sm">{{ $error }}</li>
            @endforeach
        </ul>
        @endif

        <form method="POST" action="{{ route('customerAuth.sendResetLinkEmail') }}" class="mt-4">

            @csrf

            <div>
                <input type="email" name="email" autocapitalize="off" autocorrect="off" autocomplete="off"
                    aria-label="Email"
                    class="relative block w-full px-3 py-2 text-site-text placeholder-site-muted border border-site-border rounded-md appearance-none focus:outline-hidden focus:shadow-outline-blue focus:border-site-focus focus:z-10 sm:text-sm sm:leading-5"
                    placeholder="Email" />

            </div>

            <div class="mt-6">
                <button type="submit"
                    class="relative flex justify-center w-full px-6 py-3 text-sm font-medium text-site-on-primary transition duration-150 ease-in-out bg-site-primary border border-transparent rounded-md group hover:bg-site-primary-hover focus:outline-hidden focus:border-site-focus focus:ring-2 focus:ring-site-focus active:bg-site-primary-hover">
                    Enviar
                </button>
            </div>

        </form>
    </div>
</div>

@endsection