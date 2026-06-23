@extends('customer-auth::layouts.master')

@section('content')
    <div id="app">
        @if (session('passwordResetMessage'))
            <div class="border bg-green-600 py-3 text-center text-white">
                {{ session('passwordResetMessage') }}
            </div>
        @endif

        <login-form placeholder="Usuário"></login-form>
    </div>
@endsection
