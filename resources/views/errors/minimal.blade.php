<!DOCTYPE html>
<html lang="en" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @include('layouts.seo')

        @vite([
            'resources/css/app.css',
            'resources/scss/bootstrap.scss',
            'resources/js/app.js'
        ])

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />

        @stack('styles')

    </head>
    <body class="antialiased h-full">
    <div class="min-h-full bg-white px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
        <div class="mx-auto max-w-max">
            <main class="sm:flex">
                <p class="text-4xl font-bold tracking-tight text-[#244999] sm:text-5xl">@yield('code')</p>
                <div class="sm:ml-6">
                    <div class="sm:border-l sm:border-gray-200 sm:pl-6">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">@yield('title')</h1>
                        <p class="mt-1 text-base text-gray-500">@yield('message')</p>
                    </div>
                    <div class="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                        <a href="#!" onclick="window.history.back()" class="inline-flex items-center rounded-md border border-transparent bg-[#244999] text-white px-4 py-2 text-sm font-medium text-white shadow-sm">
                            Retour
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @vite(['resources/js/bootstrap.js'])
    </body>
</html>
