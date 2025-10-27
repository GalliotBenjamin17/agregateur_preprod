@extends('gdpr.layouts.structure')

@section('subTitle', 'RGPD - Consultation de vos données')

@section('gdpr-content')
    <div>
        <h1 class="mb-1 text-base flex space-x-2 items-center">
            <span class="font-semibold text-lg">
                Données sur le profil
            </span>
        </h1>

        <div class="py-5">
            <x-information-alert
                type="warning"
                title="Vous trouverez un résumé des informations que nos stockons sur vous. "
                message="Pour plus de détails sur chacun des éléments liés (étiquettes, événements, etc.), vous pouvez télécharger vos données dans l'onglet téléchargement."
            />
        </div>

        <div class="mt-2 space-y-4">
            @if($gdprRequest->user)
                <div class="grid grid-cols-1 filament-forms-component-container gap-6">
                    <div class="col-span-full">
                        <fieldset class="filament-forms-fieldset-component rounded-xl shadow-sm border border-gray-300 p-6">
                            <legend class="text-sm leading-tight font-medium px-2 -ml-2">
                                Dénomination
                            </legend>

                            <div class="grid grid-cols-1   lg:grid-cols-2   filament-forms-component-container gap-6">
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Prénom
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->first_name }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Nom
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->last_name }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                    </div>

                    <div class="col-span-full">
                        <fieldset class="filament-forms-fieldset-component rounded-xl shadow-sm border border-gray-300 p-6">
                            <legend class="text-sm leading-tight font-medium px-2 -ml-2">
                                Données de contact
                            </legend>

                            <div class="grid grid-cols-1   lg:grid-cols-2   filament-forms-component-container gap-6">
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Email
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->email ?? "Aucune information" }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Téléphone
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->phone ?? "Aucune information" }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-span-full">
                        <fieldset class="filament-forms-fieldset-component rounded-xl shadow-sm border border-gray-300 p-6">
                            <legend class="text-sm leading-tight font-medium px-2 -ml-2">
                                Information de paiement
                            </legend>

                            <div class="grid grid-cols-1   lg:grid-cols-2   filament-forms-component-container gap-6">
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        IBAN
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->iban ?? "Aucune information" }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        BIC
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->bic ?? "Aucune information" }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-span-full">
                        <fieldset class="filament-forms-fieldset-component rounded-xl shadow-sm border border-gray-300 p-6">
                            <legend class="text-sm leading-tight font-medium px-2 -ml-2">
                                Adresse postale
                            </legend>

                            <div class="grid grid-cols-1   lg:grid-cols-2   filament-forms-component-container gap-6">
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Adresse 1
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->address_1 ?? "Aucune information" }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Complément
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->address_2 ?? "Aucune information" }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Code postal
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->address_postal_code ?? "Aucune information" }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-1" wire:key="mLgEdb8x8vyFK88ht3Pv.user.phone_2.Filament\Forms\Components\TextInput">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                    <span class="text-sm font-medium leading-4 text-gray-700">
                                                        Ville
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                <div class="flex-1">
                                                    <input type="text" value="{{ $gdprRequest->user->address_city ?? 'Aucune information' }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    @if($gdprRequest->user->nuance)
                        <div class="col-span-full">
                            <fieldset class="filament-forms-fieldset-component rounded-xl shadow-sm border border-gray-300 p-6">
                                <legend class="text-sm leading-tight font-medium px-2 -ml-2">
                                    Données politiques
                                </legend>

                                <div class="grid grid-cols-1   lg:grid-cols-2   filament-forms-component-container gap-6">
                                    <div class="col-span-1">
                                        <div class="filament-forms-field-wrapper">
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                                                    <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="user.first_name">
                                                        <span class="text-sm font-medium leading-4 text-gray-700">
                                                            Nuance politique
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                                                    <div class="flex-1">
                                                        <input type="text" value="{{ $gdprRequest->user->nuance?->name ?? 'Aucune information' }}" class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 border-gray-300" disabled readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    @endif
                </div>

                <div class="mt-10">
                    <h1 class="mb-1 text-base flex space-x-2 items-center">
                        <span class="font-semibold text-lg">
                            Organisations
                        </span>
                    </h1>
                    <x-information-alert
                        title="Les organisations permettent de vous relier à une structure du territoire (associations, entreprises, etc)."
                        message="Par exemple, si vous êtes gérant d'une association, l'administrateur pourra vous lier à cette dernière pour vous contacter s'il traite d'un sujet en lien avec votre association. Idem pour une entreprise pour un accompagnement."
                    />
                    <div class="mt-5">
                        @if(sizeof($gdprRequest->user?->organizations) > 0)
                            @foreach($gdprRequest->user->organizations as $organization)
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-0.5 text-sm font-medium text-gray-800">
                                    {{ $organization?->name ?? 'Entité supprimée' }}
                                </span>
                            @endforeach
                        @else
                            <span class="italic text-sm">Aucune entité n'est affiliée à votre profil</span>
                        @endif
                    </div>
                </div>
            @else
                Aucune donnée liée à cette adresse email
            @endif
        </div>

    </div>
@endsection
