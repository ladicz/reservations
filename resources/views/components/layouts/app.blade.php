<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{__('Reservations')}}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-black bg-gray-400">
        <header class="bg-gray-700 text-blue-400">
            <nav class="mx-auto flex w-full max-w-screen-2xl flex-wrap items-center gap-x-4 gap-y-2 p-4">
                <x-nav-item href="/">
                        {{__('Tables reservation')}}
                </x-nav-item>
                @auth
                    <x-nav-item href="/reservations">{{__('My reservations')}}</x-nav-item>
                @endauth

                <div class="ml-auto flex min-w-0 flex-wrap items-center justify-end gap-x-4 gap-y-2">
                    @guest
                        <x-nav-item href="/register">{{__('Register')}}</x-nav-item>
                        <x-nav-item href="/login">{{__('Login')}}</x-nav-item>
                    @endguest
                    @auth
                        <span class="pr-2">{{__('Logged in as ') . Auth::user()->email}}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="hover:underline cursor-pointer">{{__('Logout')}}</button>
                        </form>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="flex flex-col items-center justify-center m-auto w-full mb-4">
            {{ $slot }}
        </main>

    </body>
</html>
