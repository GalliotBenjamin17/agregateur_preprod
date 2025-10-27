<!DOCTYPE html>
<html lang="fr-FR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('layouts.seo')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />

    @vite([
        'resources/css/app.css',
        'resources/scss/bootstrap.scss',
        'resources/js/app.js'
    ])

    @livewireStyles
    <link href="{{ asset('css/mobiscroll.javascript.min.css') }}" rel="stylesheet" type="text/css">
    @stack('styles')

</head>
<body class="antialiased h-full bg-[#f6f7f9]">


    @yield('content')


@stack('scripts')
@livewireScripts

@vite(['resources/js/bootstrap.js'])
</body>
</html>



