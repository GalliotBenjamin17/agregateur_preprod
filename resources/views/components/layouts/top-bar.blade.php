<div {{ $attributes->class(['px-5 py-2 bg-white dark:bg-night-dark flex items-center justify-between relative z-20']) }}>
    <img alt="logo" class="w-auto h-[40px]" src="{{ asset($logo) }}">

    <div class="flex items-center space-x-3">
        <button type="button" href="#!" data-bs-toggle="modal" data-bs-target="#all_reminders" class="text-gray-400 hover:text-gray-500">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
            </svg>
        </button>

        @if(request()->user()->hasRole(\App\Enums\Roles::Contributor) and userHasTenant())
            <a href="{{ route('tenant.dashboard', ['tenant' => request()->user()->tenant]) }}" class="hidden md:flex text-gray-500 hover:text-gray-600 flex items-center space-x-2 rounded-lg border border-gray-400 hover:bg-gray-50 font-semibold py-1 mb-1.5 text-sm px-3">
                <span>Mes contributions</span>
            </a>
        @endrole

        @role('admin|local_admin')

            <x-dropdown class="inline-flex items-center justify-center py-0.5 pl-2 pr-10 text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                <x-slot:trigger>
                    <x-icon.settings class="w-5 h-5 rounded-full" />

                    <span class="ml-2 leading-none translate-y-px">
                        <span>Paramètres</span>
                    </span>
                    <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                </x-slot:trigger>

                <x-dropdown.title>National</x-dropdown.title>
                <x-dropdown.divider />

                <x-dropdown.item :href="route('settings.index.tenants')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </x-slot:icon>
                    Antennes locales
                </x-dropdown.item>
                <x-dropdown.divider />


                <x-dropdown.title>Métier</x-dropdown.title>

                <x-dropdown.divider />

                <x-dropdown.item :href="route('settings.index.segmentations')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </x-slot:icon>
                    Segmentation
                </x-dropdown.item>

                <x-dropdown.item :href="route('settings.index.certifications')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </x-slot:icon>
                    Certifications
                </x-dropdown.item>

                <x-dropdown.item :href="route('settings.method-forms.index')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </x-slot:icon>
                    Méthodes
                </x-dropdown.item>

                <x-dropdown.divider />

                <x-dropdown.item href="#" onclick="event.preventDefault();document.getElementById('logout').submit();" class="hover:bg-red-100 items-center text-red-500">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" x2="9" y1="12" y2="12"></line></svg>
                    </x-slot:icon>
                    Log out
                </x-dropdown.item>
            </x-dropdown>

        @endrole


            <x-dropdown class="inline-flex items-center justify-center py-0.5 pl-3 pr-10 text-sm font-medium transition-colors bg-white border rounded-md text-neutral-700 hover:bg-neutral-100 active:bg-white focus:bg-white focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                <x-slot:trigger>
                    <img src="{{ request()->user()->avatar }}" onerror="this.onerror=null; this.src='{{ asset('img/empty/avatar.svg') }}'"  class="object-cover w-5 h-5 border rounded-full border-neutral-200" />
                    <span class="ml-2 leading-none translate-y-px">
                                <span>{{ \Illuminate\Support\Str::limit(request()->user()->name, 20) }}</span>
                            </span>
                    <svg class="absolute right-0 w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
                </x-slot:trigger>

                <x-dropdown.title>Mon compte</x-dropdown.title>

                <x-dropdown.divider />

                <x-dropdown.item href="{{ route('profile.show') }}">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </x-slot:icon>
                    Profile
                </x-dropdown.item>

                <x-dropdown.divider />

                <x-dropdown.item href="#" onclick="event.preventDefault();document.getElementById('logout').submit();" class="hover:bg-red-100 items-center text-red-500">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" x2="9" y1="12" y2="12"></line></svg>
                    </x-slot:icon>
                    Log out
                </x-dropdown.item>
            </x-dropdown>

    </div>
    <form class="hidden" id="logout" method="POST" action="{{ route('logout') }}">
        @csrf
    </form>
</div>
