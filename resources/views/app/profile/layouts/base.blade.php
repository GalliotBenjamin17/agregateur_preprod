<x-dynamic-component :component="$tenant ? 'app-contributors' : 'app-layout'" :tenant="$tenant">
    <x-slot:content>
        @section('title', "Profil")

        <div @class([
            'max-w-[75rem] mx-auto mt-5' => $tenant,
        ])>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-3 bg-white @if(!$tenant) rounded-lg overflow-hidden border border-gray-300 @endif" @if(!$tenant) style="min-height: calc(100vh - 140px)" @endif>
                <div class="md:col-span-2 @if(!$tenant) p-2 @endif">
                    <nav aria-label="Sidebar">
                        <div class="space-y-1">
                            <x-layouts.settings-tab
                                title="Informations"
                                :active="request()->routeIs('profile.show')"
                                :href="route('profile.show')"
                            >
                                <x-slot:icon>
                                    <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Données"
                                :active="request()->routeIs('profile.datas')"
                                :href="route('profile.datas')"
                            >
                                <x-slot:icon>
                                    <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>

                            <x-layouts.settings-tab
                                title="Sécurité"
                                :active="request()->routeIs('profile.security')"
                                :href="route('profile.security')"
                            >
                                <x-slot:icon>
                                    <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </x-slot:icon>
                            </x-layouts.settings-tab>
                        </div>
                    </nav>
                </div>
                <div class="sm:col-span-10 bg-white @if(!$tenant)) p-2 md:p-2.5 sm:p-[1rem] @endif">
                    <x-layouts.card
                        group-name="Profil"
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
                    </x-layouts.card>

                    @isset($cardContent)
                        {{ $cardContent }}
                    @endisset
                </div>
            </div>
        </div>
    </x-slot:content>

    <x-slot:modals>
        @isset($modals)
            {{ $modals }}
        @endisset
    </x-slot:modals>
</x-dynamic-component>
