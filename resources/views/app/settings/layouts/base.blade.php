<x-app-layout>
    <x-slot:content>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3 bg-white rounded-md border border-gray-300" style="min-height: calc(100vh - 140px)">
            <div class="md:col-span-2 p-2">
                <nav aria-label="Sidebar">
                    <div class="space-y-1">
                        @role('admin|local_admin')
                            <x-layouts.settings-tab
                                title="Administrateurs"
                                :active="request()->routeIs('settings.index.admins')"
                                :href="route('settings.index.admins')"
                            >
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>
                        @endrole

                        @role(\App\Enums\Roles::LocalAdmin)
                            <x-layouts.settings-tab
                                title="Mon antenne locale"
                                :active="request()->routeIs('settings.show.tenants')"
                                :href="route('settings.show.tenants', ['tenant' => request()->user()->tenant])"
                            >
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>
                        @endrole

                        @role(\App\Enums\Roles::Admin)
                            <x-layouts.settings-tab
                                title="Antennes locales"
                                :active="request()->routeIs('settings.index.tenants')"
                                :href="route('settings.index.tenants')"
                            >
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Variables"
                                :active="request()->routeIs('settings.index.variables')"
                                :href="route('settings.index.variables')"
                            >
                                <x-slot:icon>
                                    <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Certifications"
                                :active="request()->routeIs('settings.index.certifications')"
                                :href="route('settings.index.certifications')"
                            >
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Segmentations"
                                :active="request()->routeIs('settings.index.segmentations')"
                                :href="route('settings.index.segmentations')"
                            >
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Méthodes"
                                :active="request()->routeIs('settings.method-forms.index', 'settings.method-form-groups.show', 'settings.method-form-groups.method-form.show')"
                                :href="route('settings.method-forms.index')"
                            >
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h1.5m9 0h-9" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Logs de connexion"
                                :active="request()->routeIs('settings.index.logs')"
                                :href="route('settings.index.logs')"
                            >
                                <x-slot:icon>
                                    <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Types d'organisations"
                                :active="request()->routeIs('settings.index.organization-types', 'organization-types.*')"
                                :href="route('settings.index.organization-types')"
                            >
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Badges"
                                :active="request()->routeIs('settings.badges.index')"
                                :href="route('settings.badges.index')"
                            >
                                <x-slot:icon>

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                                    </svg>

                                </x-slot:icon>
                            </x-layouts.settings-tab>
                        @endrole

                        <x-layouts.settings-tab
                            title="Bornes"
                            :active="request()->routeIs('settings.index.terminals')"
                            :href="route('settings.index.terminals')"
                        >
                            <x-slot:icon>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </x-slot:icon>
                        </x-layouts.settings-tab>

                    </div>
                </nav>
            </div>
            <div class="sm:col-span-10 bg-white p-2 md:p-2.5 sm:p-[1rem]">
                <x-layouts.card
                    group-name="Paramètres"
                    :name="$pageName"
                    :thin-padding="false"
                >
                    <x-slot:icon>
                        <div class="h-[40px] w-[40px] bg-[#e36f92] rounded-md flex ">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 mx-auto my-auto text-white">
                                <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </x-slot:icon>

                    <x-slot:actions>
                        @isset($actions)
                            {{ $actions }}
                        @endisset
                    </x-slot:actions>
                </x-layouts.card>

                @isset($cardContent)
                    {{ $cardContent }}
                @endisset
            </div>
        </div>
    </x-slot:content>

    <x-slot:modals>
        @isset($modals)
            {{ $modals }}
        @endisset
    </x-slot:modals>
</x-app-layout>
