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
    <link href="https://fonts.bunny.net/css?family=alexandria:100,200,300,400,500,600,700,800,900" rel="stylesheet" />

    @filamentStyles
    <link href="{{ asset('css/mobiscroll.javascript.min.css') }}" rel="stylesheet" type="text/css">
    @stack('styles')

    @if(! $blankLayout)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btn = document.querySelector('#dark-mode-toggle');
                const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;

                if (currentTheme) {
                    document.documentElement.setAttribute('data-theme', currentTheme);

                    if (currentTheme === 'dark') {
                        document.documentElement.classList.add('dark');
                    }
                }

                btn.addEventListener('click', function () {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                });
            });
        </script>
    @endif

    <style>
        body {
            font-family: 'Alexandria', sans-serif !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="relative antialiased h-full">
        @if($blankLayout)
            {{ $slot }}
        @else
            <div x-data="{ open: false }" @keydown.window.escape="open = false">

                <div x-show="open" class="relative z-50 lg:hidden" x-ref="dialog" aria-modal="true" style="display: none;">

                    <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80" style="display: none;"></div>

                    <div class="fixed inset-0 flex">

                        <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative mr-16 flex w-full max-w-xs flex-1" @click.away="open = false" style="display: none;">

                            <div x-show="open" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute left-full top-0 flex w-16 justify-center pt-5" style="display: none;">
                                <button type="button" class="-m-2.5 p-2.5" @click="open = false">
                                    <span class="sr-only">Fermer le menu</span>
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Sidebar component, swap this element with another sidebar if you like -->
                            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 ring-1 ring-white/10">
                                <div class="flex h-16 shrink-0 items-center">
                                    <img class="h-8 w-auto" src="{{ $tenant->logo_white }}" alt="{{ $tenant->name }}" onerror="this.onerror=null;this.src='{{ asset('img/logos/cooperative-carbone/logo_white.png') }}';">
                                </div>
                                <nav class="flex flex-1 flex-col">
                                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                        <li>
                                            <ul role="list" class="-mx-2 space-y-1">


                                                <x-layouts.sidebar-desktop-link title="Accueil" :link="route('tenant.dashboard', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.dashboard')">
                                                    <x-slot name="icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                                        </svg>
                                                    </x-slot>
                                                </x-layouts.sidebar-desktop-link>

                                                <x-layouts.sidebar-desktop-link title="Contributions" :link="route('tenant.donations.index', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.donations.index')">
                                                    <x-slot name="icon">
                                                        <svg viewBox="0 0 24 24" width="20" height="20" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                            <path fill="currentColor" d="M10.5,3C8,3 6,5 6,7.5C6,8.11 6.13,8.71 6.37,9.27C5.5,10.12 5,11.28 5,12.5C5,15 7,17 9.5,17C10,17 10.5,16.89 11,16.72V21H13V15.77C13.5,15.91 14,16 14.5,16A5.5,5.5 0 0,0 20,10.5A5.5,5.5 0 0,0 14.5,5C14.41,5 14.33,5 14.24,5C13.41,3.76 12,3 10.5,3M10.5,5C11.82,5 12.91,6.03 13,7.35C13.46,7.12 14,7 14.5,7A3.5,3.5 0 0,1 18,10.5A3.5,3.5 0 0,1 14.5,14C13.54,14 12.63,13.61 11.96,12.91C11.76,14.12 10.72,15 9.5,15A2.5,2.5 0 0,1 7,12.5C7,11.12 7.8,10.54 9,9.79C8.2,8.76 8,8.16 8,7.5A2.5,2.5 0 0,1 10.5,5Z"></path>
                                                        </svg>
                                                    </x-slot>
                                                </x-layouts.sidebar-desktop-link>

                                                @if($projects->count() > 0)
                                                    <x-layouts.sidebar-desktop-link title="Suivi des projets" :link="route('tenant.projects.show', ['tenant' => userTenant(), 'project' => $projects->first(), 'organization' => $organization])" :active="request()->routeIs('tenant.projects.show')">
                                                        <x-slot name="icon">
                                                            <svg height="800" width="800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 459.5 459.5" xml:space="preserve" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6"><path fill="currentColor" d="M57.209 114.262c-2.694 24.018.306 58.49 51.104 72.307 62.557 17.017 128.982-17.122 157.053-34.333a5.673 5.673 0 0 0 2.583-6.017 5.672 5.672 0 0 0-4.809-4.445c-16.989-2.223-42.893-8.021-54.135-23.057-47.583-63.629-76.913-58.553-94.328-58.553-19.788 0-35.817 9.242-45.927 23.184-9.904-10.608-19.772-23.753-29.096-40.065-4.11-7.192-13.272-9.689-20.467-5.579-7.192 4.11-9.69 13.273-5.579 20.466 13.739 24.034 28.676 42.261 43.601 56.092zM456.739 396.453l-92.748-145.746c-18.484-23.987-48.681-37.119-79.083-33.809l-101.367 11.027c-14.622 1.591-25.186 14.733-23.595 29.355 1.59 14.621 14.732 25.184 29.355 23.596l96.907-10.542c-176.643 34.389-159.795 31.386-165.694 31.612L44.285 234.46c-11.013-9.749-27.843-8.726-37.593 2.287s-8.726 27.844 2.287 37.594l84.228 74.568a26.586 26.586 0 0 0 18.677 6.671l179.071-6.887 42.769 63.354c4.548 6.736 11.639 10.726 19.13 11.561l.102.168h88.743a17.803 17.803 0 0 0 15.04-27.323z"/></svg>
                                                        </x-slot>
                                                    </x-layouts.sidebar-desktop-link>
                                                @endif


                                                <x-layouts.sidebar-desktop-link title="Ressources" :link="route('tenant.resources.index', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.resources.index')">
                                                    <x-slot name="icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                                        </svg>
                                                    </x-slot>
                                                </x-layouts.sidebar-desktop-link>

                                                <x-layouts.sidebar-desktop-link title="FAQ" :link="route('tenant.faq.index', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.faq.index')">
                                                    <x-slot name="icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                                        </svg>

                                                    </x-slot>
                                                </x-layouts.sidebar-desktop-link>

                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- Static sidebar for desktop -->
                <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-60 lg:flex-col">
                    <!-- Sidebar component, swap this element with another sidebar if you like -->
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
                        <div class="flex h-16 shrink-0 items-center">
                            <img class="h-8 w-auto" src="{{ $tenant->logo_white }}" alt="{{ $tenant->name }}" onerror="this.onerror=null;this.src='{{ asset('img/logos/cooperative-carbone/logo_white.png') }}';">
                        </div>

                        @if($userOrganizations->count() > 0)
                            <x-dropdown dropdownPosition="right" class="inline-flex items-center justify-center py-0.5 pl-3 pr-10 w-full text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                                <x-slot:trigger>
                                <span class="py-1.5 leading-none translate-y-px">
                                    @if($organization?->id)
                                        <span>{{ $organization->name }}</span>
                                    @else
                                        <span>Mes contributions</span>
                                    @endif
                                </span>
                                    <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                </x-slot:trigger>

                                <x-dropdown.title>Autres interfaces</x-dropdown.title>

                                <x-dropdown.divider />

                                @if($organization?->id)
                                    <x-dropdown.item :href="route(request()->routeIs('tenant.projects.show') ? 'tenant.dashboard' : request()->route()->getName(), [...request()->route()->parameters(), 'organization' => null, 'personal_view' => 'true']) ">
                                        Mes contributions
                                    </x-dropdown.item>

                                    @foreach($userOrganizations->except($organization->id) as $userOrganization)
                                        <x-dropdown.item :href="route(request()->routeIs('tenant.projects.show') ? 'tenant.dashboard' : request()->route()->getName(), [...request()->route()->parameters(), 'organization' => $userOrganization]) ">
                                            {{ $userOrganization->name }}
                                        </x-dropdown.item>
                                    @endforeach

                                @else
                                    @foreach($userOrganizations as $userOrganization)
                                        <x-dropdown.item :href="route(request()->routeIs('tenant.projects.show') ? 'tenant.dashboard' : request()->route()->getName(), [...request()->route()->parameters(), 'organization' => $userOrganization]) ">
                                            {{ $userOrganization->name }}
                                        </x-dropdown.item>
                                    @endforeach
                                @endif
                            </x-dropdown>
                        @endif

                        <nav class="flex flex-1 flex-col">
                            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                <li>

                                    <ul role="list" class="-mx-2 space-y-1">


                                        <x-layouts.sidebar-desktop-link title="Accueil" :link="route('tenant.dashboard', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.dashboard')">
                                            <x-slot name="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                                </svg>
                                            </x-slot>
                                        </x-layouts.sidebar-desktop-link>

                                        <x-layouts.sidebar-desktop-link title="Contributions" :link="route('tenant.donations.index', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.donations.index')">
                                            <x-slot name="icon">
                                                <svg viewBox="0 0 24 24" width="20" height="20" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                    <path fill="currentColor" d="M10.5,3C8,3 6,5 6,7.5C6,8.11 6.13,8.71 6.37,9.27C5.5,10.12 5,11.28 5,12.5C5,15 7,17 9.5,17C10,17 10.5,16.89 11,16.72V21H13V15.77C13.5,15.91 14,16 14.5,16A5.5,5.5 0 0,0 20,10.5A5.5,5.5 0 0,0 14.5,5C14.41,5 14.33,5 14.24,5C13.41,3.76 12,3 10.5,3M10.5,5C11.82,5 12.91,6.03 13,7.35C13.46,7.12 14,7 14.5,7A3.5,3.5 0 0,1 18,10.5A3.5,3.5 0 0,1 14.5,14C13.54,14 12.63,13.61 11.96,12.91C11.76,14.12 10.72,15 9.5,15A2.5,2.5 0 0,1 7,12.5C7,11.12 7.8,10.54 9,9.79C8.2,8.76 8,8.16 8,7.5A2.5,2.5 0 0,1 10.5,5Z"></path>
                                                </svg>
                                            </x-slot>
                                        </x-layouts.sidebar-desktop-link>

                                        @if($projects->count() > 0)
                                            <x-layouts.sidebar-desktop-link title="Suivi des projets" :link="route('tenant.projects.show', ['tenant' => userTenant(), 'project' => $projects->first(), 'organization' => $organization])" :active="request()->routeIs('tenant.projects.show')">
                                                <x-slot name="icon">
                                                    <svg height="800" width="800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 459.5 459.5" xml:space="preserve" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6"><path fill="currentColor" d="M57.209 114.262c-2.694 24.018.306 58.49 51.104 72.307 62.557 17.017 128.982-17.122 157.053-34.333a5.673 5.673 0 0 0 2.583-6.017 5.672 5.672 0 0 0-4.809-4.445c-16.989-2.223-42.893-8.021-54.135-23.057-47.583-63.629-76.913-58.553-94.328-58.553-19.788 0-35.817 9.242-45.927 23.184-9.904-10.608-19.772-23.753-29.096-40.065-4.11-7.192-13.272-9.689-20.467-5.579-7.192 4.11-9.69 13.273-5.579 20.466 13.739 24.034 28.676 42.261 43.601 56.092zM456.739 396.453l-92.748-145.746c-18.484-23.987-48.681-37.119-79.083-33.809l-101.367 11.027c-14.622 1.591-25.186 14.733-23.595 29.355 1.59 14.621 14.732 25.184 29.355 23.596l96.907-10.542c-176.643 34.389-159.795 31.386-165.694 31.612L44.285 234.46c-11.013-9.749-27.843-8.726-37.593 2.287s-8.726 27.844 2.287 37.594l84.228 74.568a26.586 26.586 0 0 0 18.677 6.671l179.071-6.887 42.769 63.354c4.548 6.736 11.639 10.726 19.13 11.561l.102.168h88.743a17.803 17.803 0 0 0 15.04-27.323z"/></svg>
                                                </x-slot>
                                            </x-layouts.sidebar-desktop-link>
                                        @endif


                                        <x-layouts.sidebar-desktop-link title="Ressources" :link="route('tenant.resources.index', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.resources.index')">
                                            <x-slot name="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                                </svg>
                                            </x-slot>
                                        </x-layouts.sidebar-desktop-link>

                                        <x-layouts.sidebar-desktop-link title="FAQ" :link="route('tenant.faq.index', ['tenant' => userTenant(), 'organization' => $organization])" :active="request()->routeIs('tenant.faq.index')">
                                            <x-slot name="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-white group-hover:text-w mr-3 flex-shrink-0 h-6 w-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                                </svg>

                                            </x-slot>
                                        </x-layouts.sidebar-desktop-link>



                                    </ul>
                                </li>

                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="lg:pl-60">
                    <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-slate-200 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 dark:bg-slate-800 dark:text-slate-100 dark:border-gray-700">
                        <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="open = true">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                            </svg>
                        </button>

                        <!-- Separator -->
                        <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true"></div>

                        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                            <div class="relative flex-1 pt-4 hidden md:flex">

                            </div>


                            <div class="flex items-center gap-x-4 lg:gap-x-6">

                                <button id="dark-mode-toggle" class="py-1">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 hidden dark:inline tippy" data-tippy-content="Mode clair">
                                        <path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-2.25A.75.75 0 0 1 12 18ZM7.758 17.303a.75.75 0 0 0-1.061-1.06l-1.591 1.59a.75.75 0 0 0 1.06 1.061l1.591-1.59ZM6 12a.75.75 0 0 1-.75.75H3a.75.75 0 0 1 0-1.5h2.25A.75.75 0 0 1 6 12ZM6.697 7.757a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 0 0-1.061 1.06l1.59 1.591Z" />
                                    </svg>


                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-600 dark:hidden tippy" data-tippy-content="Mode sombre">
                                        <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" clip-rule="evenodd" />
                                    </svg>

                                </button>


                                <!-- Separator -->
                                <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10" aria-hidden="true"></div>

                                <x-dropdown class="inline-flex items-center justify-center py-1 pl-3 pr-10 text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                                    <x-slot:trigger>
                                        <img src="{{ request()->user()->avatar }}" onerror="this.onerror=null; this.src='{{ asset('img/empty/avatar.svg') }}'"  class="object-cover w-5 h-5 border rounded-full border-neutral-200" />
                                        <span class="ml-2 leading-none translate-y-px">
                                    <span>{{ \Illuminate\Support\Str::limit(request()->user()->name, 20) }}</span>
                                </span>
                                        <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                    </x-slot:trigger>

                                    <x-dropdown.title>Paramètres</x-dropdown.title>

                                    <x-dropdown.divider />

                                    <x-dropdown.item :href="route('tenant.profile.details', ['tenant' => userTenant(), 'organization' => $organization])">
                                        <x-slot:icon>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        </x-slot:icon>
                                        Mon profil
                                    </x-dropdown.item>

                                    <x-dropdown.item :href="route('tenant.profile.notifications', ['tenant' => userTenant()])">
                                        <x-slot:icon>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                                            </svg>
                                        </x-slot:icon>
                                        Notifications
                                    </x-dropdown.item>

                                    <x-dropdown.item :href="route('tenant.profile.rgpd', ['tenant' => userTenant()])">
                                        <x-slot:icon>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                            </svg>
                                        </x-slot:icon>
                                        RGPD
                                    </x-dropdown.item>

                                    <x-dropdown.divider />

                                    <x-dropdown.item href="#!" onclick="event.preventDefault();document.getElementById('logout').submit();" class="hover:bg-red-100 items-center text-red-500">
                                        <x-slot:icon>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" x2="9" y1="12" y2="12"></line></svg>
                                        </x-slot:icon>
                                        Déconnexion
                                    </x-dropdown.item>
                                </x-dropdown>
                            </div>
                        </div>
                    </div>
                </div>

                <main class="lg:pl-60 py-5 bg-slate-50 min-h-screen flex flex-col dark:bg-slate-800 dark:text-slate-100">
                    <div class="px-6 max-w-[72rem] mx-auto w-full pb-36">

                        <div class="flex items-center justify-between">

                            <div class="block lg:hidden ">
                                @if($userOrganizations->count() > 0)
                                    <x-dropdown dropdownPosition="right" class="inline-flex items-center justify-center py-0.5 pl-3 pr-10 w-full text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                                        <x-slot:trigger>
                                <span class="py-1.5 leading-none translate-y-px">
                                    @if($organization?->id)
                                        <span>{{ $organization->name }}</span>
                                    @else
                                        <span>Mes contributions</span>
                                    @endif
                                </span>
                                            <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                        </x-slot:trigger>

                                        <x-dropdown.title>Autres interfaces</x-dropdown.title>

                                        <x-dropdown.divider />

                                        @if($organization?->id)
                                            <x-dropdown.item :href="route(request()->routeIs('tenant.projects.show') ? 'tenant.dashboard' : request()->route()->getName(), [...request()->route()->parameters(), 'organization' => null, 'personal_view' => 'true']) ">
                                                Mes contributions
                                            </x-dropdown.item>

                                            @foreach($userOrganizations->except($organization->id) as $userOrganization)
                                                <x-dropdown.item :href="route(request()->routeIs('tenant.projects.show') ? 'tenant.dashboard' : request()->route()->getName(), [...request()->route()->parameters(), 'organization' => $userOrganization]) ">
                                                    {{ $userOrganization->name }}
                                                </x-dropdown.item>
                                            @endforeach

                                        @else
                                            @foreach($userOrganizations as $userOrganization)
                                                <x-dropdown.item :href="route(request()->routeIs('tenant.projects.show') ? 'tenant.dashboard' : request()->route()->getName(), [...request()->route()->parameters(), 'organization' => $userOrganization]) ">
                                                    {{ $userOrganization->name }}
                                                </x-dropdown.item>
                                            @endforeach
                                        @endif
                                    </x-dropdown>
                                @endif
                            </div>

                            @if(request()->routeIs('tenant.dashboard', 'tenant.projects.show', 'tenant.donations.index') and $projects->count() > 0)
                                <x-dropdown dropdownPosition="left" class="inline-flex items-center justify-center py-0.5 pl-3 pr-10 text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                                    <x-slot:trigger>
                                    <span class="py-1.5 leading-none translate-y-px">
                                        Vos projets
                                    </span>
                                        <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                                    </x-slot:trigger>

                                    <x-dropdown.title>
                                        Vos projets
                                    </x-dropdown.title>

                                    <x-dropdown.divider />

                                    @foreach($projects as $project)
                                        <x-dropdown.item :href="route('tenant.projects.show', ['tenant' => userTenant(), 'project' => $project, 'organization' => $organization])">
                                            {{ $project->display_name ?? $project->name }}
                                        </x-dropdown.item>
                                    @endforeach

                                </x-dropdown>
                            @endif
                        </div>

                        {{ $slot }}

                    </div>

                    <div class="pt-4 text-sm border-t-2 border-gray-300 dark:border-gray-700 mt-auto">
                        <ul class="max-w-[72rem] mx-auto px-6 flex flex-col md:flex-row items-center md:divide-x divide-gray-700 [&>li]:px-2">
                            <li>©{{ now()->year }} {{ $tenant->name }}</li>
                            <li class="hover:underline"><a href="{{ $tenant->public_url }}" target="_blank">Site internet</a></li>
                            @if($tenant->support_email)
                                <li class="hover:underline tippy" data-tippy-content="{{ $tenant->support_email }}"><a href="mailto:{{ $tenant->support_email }}" class="">Contact support</a></li>
                            @endif
                        </ul>
                    </div>
                </main>
            </div>
        @endif




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
