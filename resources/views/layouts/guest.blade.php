<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('layouts.seo')

        <!-- Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/scss/bootstrap.scss',
            'resources/js/app.js'
        ])
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />

        @filamentStyles

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>
    <body class="h-full">
        {{ $slot }}


        <div id="alerts-bag" class="fixed top-4 right-4 left-4 sm:left-[initial] flex gap-2 flex-col-reverse z-60">
            @stack('alerts')
        </div>

        @vite(['resources/js/bootstrap.js'])
        @filamentScripts
        @livewire('notifications')

        <script async>
            @if($errors->any())
                @foreach($errors->all() as $error)
                    setTimeout(function () {
                        new FilamentNotification().title('Error').body("{{ $error }}").danger().send();
                    }, 500);
                @endforeach
            @endif

            @if(Session::has('alert'))
                setTimeout(function () {
                    new FilamentNotification().title("{{ Session::get('alert') }}").danger().send();
                }, 500);
            @endif

            @if(Session::has('success'))
                setTimeout(function () {
                    new FilamentNotification().title("{{ Session::get('success') }}").success().send();
                }, 500);
            @endif

            @if(Session::has('info'))
                setTimeout(function () {
                    new FilamentNotification().title("{{ Session::get('info') }}").warning().send();
                }, 500);
            @endif
        </script>
    </body>
</html>
