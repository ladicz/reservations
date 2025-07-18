<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <h1 class="text-3xl text-blue-600 mb-3">Došlo k chybě</h1>
        <p>Omlouváme se, nastala neočekávaná chyba.</p>
        <a href="/" class="hover:underline text-blue-400 mt-3">{{__('Hlavní stránka')}}</a>
        @if(config('app.debug'))
            <h3>{{ get_class($exception) }}</h3>
            <p>{{ $exception->getMessage() }}</p>
            <pre>{{ $exception->getTraceAsString() }}</pre>
        @endif
    </body>
</html>
