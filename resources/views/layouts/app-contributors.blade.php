<!DOCTYPE html>
<html lang="fr-FR" class="h-full">
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

    @filamentStyles
    <link href="{{ asset('css/mobiscroll.javascript.min.css') }}" rel="stylesheet" type="text/css">
    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="relative antialiased h-full">
    <div class="relative bg-white">
        <div class="border-b border-gray-300">
            <div class="max-w-[75rem]  mx-auto py-3 px-3 flex items-center justify-between">
                <a href="{{ route('tenant.dashboard', ['tenant' => $tenant]) }}">
                    <img class="h-9 rounded-md" src="{{ asset($tenant->logo) }}">
                </a>
                <div class="text-[#404040] text-[16px] hidden md:flex items-center space-x-10">
                    <a @class([
                        'hover:underline',
                        'font-bold' => request()->routeIs('tenant.dashboard'),
                    ])
                       href="{{ route('tenant.dashboard', ['tenant' => $tenant]) }}">
                        Accueil
                    </a>

                    <a @class([
                        'hover:underline',
                        'font-bold' => request()->routeIs('tenant.donations.index'),
                    ]) href="{{ route('tenant.donations.index', ['tenant' => $tenant]) }}">
                        Contributions
                    </a>

                    <a href="{{ $tenant->public_url }}" target="_blank" class="flex items-center space-x-2 hover:underline">
                        <span>Site externe</span>
                        <x-icon.lien_externe class="h-4 w-4 text-gray-500" />
                    </a>

                </div>
                <div x-data="{ open: false }" @keydown.escape.stop="open = false;" @click.away="open = false" class="relative ml-4 flex-shrink-0">
                    <button x-on:click="open = !open" class="px-[16px] py-2 rounded-md bg-gray-300 font-semibold border border-gray-500 hover:opacity-800 text-[#404040]">
                        Mon profil
                    </button>
                    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-50 mt-0.5 w-[300px] md:w-[350px] origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" @keydown.tab="open = false" @keyup.space.prevent="open = false;">
                        <div class="block flex items-start space-x-4 px-4 py-2">
                            <img class="w-[40px] h-[40px] rounded-md flex-shrink-0 bg-white" src="{{ asset(request()->user()->avatar) }}">
                            <div class="">
                                <p class="font-semibold">{{ request()->user()->name }}</p>
                                <p>{{ request()->user()->email }}</p>
                                <div class="md:flex items-center space-x-3 mt-2">
                                    <a href="{{ route('profile.security') }}" class="text-blue-500 hover:text-blue-700 hover:underline"> Dernières connexions </a>
                                    <a href="#!" onclick="event.preventDefault();document.getElementById('logout').submit();" class="text-red-500 hover:text-red-700 hover:underline"> Deconnexion </a>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-200">
                            <div class="px-4 py-1 text-uppercase text-sm font-semibold"> Accès rapides </div>
                        </div>
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50" role="menuitem" tabindex="-1"> Mon profil </a>
                        <a href="{{ route('profile.datas') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50" role="menuitem" tabindex="-1"> Mes données </a>
                        <a href="{{ route('profile.security') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50" role="menuitem" tabindex="-1"> Sécurité </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-3">
            {{ $content }}
        </div>

        <div class="max-w-5xl mx-auto mt-56 border-t border-slate-900/5 py-10">
            <img class="mx-auto h-5 w-auto text-slate-900" src="{{ asset($tenant->logo) }}">
            <p class="mt-5 text-center text-sm leading-6 text-slate-500">{{ date('Y') }} {{ $tenant->name }}. Tous droits réservés.</p>
            <div class="mt-8  flex items-center justify-center text-center space-x-4 text-sm font-semibold leading-6 text-slate-700">
                <a href="#">Condition générale d'utilisation</a>
                <div class="h-4 w-px bg-slate-500/20"></div>
                <a href="#">Politique de confidentialité</a>
            </div>
        </div>
    </div>

    <div id="alerts-bag" class="fixed top-4 right-4 left-4 sm:left-[initial] flex gap-2 flex-col-reverse z-60">
        @stack('alerts')
    </div>

    @isset($modals)
        {{ $modals }}
    @endisset

    <form class="hidden" id="logout" method="POST" action="{{ route('logout') }}">
        @csrf
    </form>

    <script src="{{ asset('js/mobiscroll.javascript.min.js') }}"></script>
    @stack('scripts')
    @filamentScripts
    @livewire('notifications')
    @vite(['resources/js/bootstrap.js'])
</body>
</html>
