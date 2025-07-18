<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Rezervace</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-black bg-gray-400">
        <header class="bg-gray-700 text-blue-400 flex justify-between">
            <nav class="max-w-screen-xxl w-full flex items-center justify-start gap-4 p-4 whitespace-nowrap">
                <x-nav-item href="/">
                        {{__('Rezervace stolu')}}
                </x-nav-item>
                @auth
                    <x-nav-item href="/reservations">{{__('Moje rezervace')}}</x-nav-item>
                @endauth

                <div class="flex items-center justify-end gap-4 w-full mr-2">
                    @guest
                        <x-nav-item href="/register">{{__('Registrace')}}</x-nav-item>
                        <x-nav-item href="/login">{{__('Přihlášení')}}</x-nav-item>
                    @endguest
                    @auth
                        <span class="pr-2">{{__('Přihlášen jako ') . Auth::user()->email}}</span>
                        <x-nav-item href="/logout">{{__('Odhlásit')}}</x-nav-item>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="flex flex-col items-center justify-center m-auto w-full mb-4">
            {{ $slot }}
        </main>

    </body>
</html>
