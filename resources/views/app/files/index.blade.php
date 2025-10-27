<x-app-layout>
    <x-slot name="content">
        @section('title', "Fichiers")

        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-12">
                <x-layouts.card
                    group-name="Fichiers"
                    name="Tous les fichiers"
                >
                    <x-slot:icon>
                        {!! \App\Helpers\IconHelper::filesIcon() !!}
                    </x-slot:icon>

                    <x-slot:actions>
                        <x-button icon data-bs-toggle="modal" data-bs-target="#add_file">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path d="M9.25 13.25a.75.75 0 001.5 0V4.636l2.955 3.129a.75.75 0 001.09-1.03l-4.25-4.5a.75.75 0 00-1.09 0l-4.25 4.5a.75.75 0 101.09 1.03L9.25 4.636v8.614z" />
                                <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
                            </svg>
                            <span>Charger un fichier</span>
                        </x-button>
                    </x-slot:actions>

                    <x-slot:content>
                        <div class="grid grid-cols-1 sm:grid-cols-12">
                            <div class="h-screen hidden sm:block col-span-2 bg-gray-light pt-[56px]">
                                <x-layouts.settings-tab
                                    title="Tous les fichiers"
                                    :active="request()->routeIs('files.index') and !str_contains(url()->full(), 'permanence')"
                                    :href="route('files.index')"
                                    :rounded="false"
                                >
                                    <x-slot:icon>
                                        <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                        </svg>
                                    </x-slot:icon>
                                </x-layouts.settings-tab>
                                <x-layouts.settings-tab
                                    title="Fichiers de la permanence"
                                    :active="request()->routeIs('files.index') and str_contains(url()->full(), 'permanence')"
                                    :href="route('files.index', ['type' => 'permanence'])"
                                    :rounded="false"
                                >
                                    <x-slot:icon>
                                        <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                        </svg>
                                    </x-slot:icon>
                                </x-layouts.settings-tab>
                            </div>
                            <div class="col-span-10">
                                <livewire:tables.files.index-table :type="request()->has('type') ? request()->get('type') : null" />
                            </div>
                        </div>
                    </x-slot:content>
                </x-layouts.card>
            </div>
        </div>
    </x-slot>

    <x-slot:modals>
        <x-modals.create-file />
    </x-slot:modals>
</x-app-layout>
