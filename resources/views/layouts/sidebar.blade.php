<div x-show="open" class="relative z-40 md:hidden" x-ref="dialog" aria-modal="true" style="display: none;">
    <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-description="Off-canvas menu backdrop, show/hide based on off-canvas menu state." class="fixed inset-0 bg-gray-600 bg-opacity-75" style="display: none;"></div>
    <div class="fixed inset-0 flex z-40">
        <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" x-description="Off-canvas menu, show/hide based on off-canvas menu state." class="relative flex-1 flex flex-col max-w-xs w-full bg-white" @click.away="open = false" style="display: none;">
            <div x-show="open" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-description="Close button, show/hide based on off-canvas menu state." class="absolute top-0 right-0 -mr-12 pt-2" style="display: none;">
                <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="open = false">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-white" x-description="Heroicon name: outline/x" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <img class="h-8 w-auto" src="{{ asset('img/logos/oomyad/com-unique.png') }}" alt="OOH MY AD">
                </div>
                <nav class="mt-5 px-2 space-y-1">
                    <x-layouts.sidebar-mobile-link title="Tableau de bord" :link="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-800 group-hover:text-gray-800 mr-3 flex-shrink-0 h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </x-slot>
                    </x-layouts.sidebar-mobile-link>
                </nav>
            </div>
            <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                <a href="#" class="flex-shrink-0 group block">
                    <div class="flex items-center">
                        <div>
                            <img class="inline-block h-10 w-10 rounded-full" src="{{ Auth::user()->avatar }}" alt="">
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-medium text-gray-700 group-hover:text-gray-900">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">
                                Mon profil
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="flex-shrink-0 w-14">
            <!-- Force sidebar to shrink to fit close icon -->
        </div>
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 ">
    <div class="flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-[#22394f]">
        <div class="flex-1 flex flex-col overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4  h-[56px] border-b border-gray-200">
                <img class="w-full" src="{{ asset('img/logos/oomyad/com-unique-semi-white.png') }}" alt="OOHMYAD">
            </div>
            <nav class="mt-5 flex-1 px-2 space-y-1">

                <x-layouts.sidebar-desktop-link title="Tableau de bord" :link="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="@if(request()->routeIs('dashboard')) text-gray-800 @else text-white @endif group-hover:text-gray-800 mr-3 flex-shrink-0 h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </x-slot>
                </x-layouts.sidebar-desktop-link>
            </nav>
        </div>
        <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
            <div class="flex-shrink-0 w-full group block">
                <div class="flex items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-50">
                            {{ Auth::user()->name }}
                        </p>
                        <div class="flex items-center space-x-3">
                            <a href="#" class="text-white font-medium text-xs hover:text-gray-100">
                                Mon profil
                            </a>
                            <span class="text-white">&bull;</span>
                            <a onclick="event.preventDefault();document.getElementById('logout').submit();" class="text-xs font-medium text-white hover:text-red-500 flex items-center space-x-1">
                                <span>Deconnexion</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>

                        <form class="hidden" id="logout" method="POST" action="{{ route('logout') }}">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
