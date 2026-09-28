<!DOCTYPE html>
<html lang="{{ html_lang() }}" dir="{{ html_dir() }}" data-native-digits="{{ locale_uses_native_digits() ? 'true' : 'false' }}">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		{{-- used in axios requests if needed --}}
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>Adm</title>
		<meta name="description" content="Adm">

		<link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
		<link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Noto+Sans+Bengali:wght@400;600;700&display=swap">

		@vite('resources/css/app.css')

	</head>

	<body>

        @yield('content')
            
        @vite('resources/js/app-auth.js')

	</body>

</html>