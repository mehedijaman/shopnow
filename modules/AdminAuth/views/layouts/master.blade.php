<!DOCTYPE html>
<html lang="pt-br">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		{{-- used in axios requests if needed --}}
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>Adm</title>
		<meta name="description" content="Adm">

		@vite('resources/css/app.css')

	</head>

	<body>

        @yield('content')
            
        @vite('resources/js/app-auth.js')

	</body>

</html>