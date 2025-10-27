@extends('gdpr.layouts.structure')

@section('gdpr-content')
    <div>
        <h1 class="mb-4 text-base flex space-x-2 items-center">
            <span class="font-bold">
                Liens rapides
            </span>
        </h1>

        <div class="grid grid-cols-4 gap-4">
            <a href="{{ route('gdpr.hub.see.index') }}" class="group block w-full sm:w-[120px] xl:w-[160px] flex flex-col text-decoration-none">
                <div class="rounded-lg h-[75px] sm:h-[90px] xl:h-[120px] bg-sky-50 group-hover:bg-sky-100 flex">
                    <div class="mx-auto my-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 xl:h-12 w-8 xl:w-12 text-sky-400 group-hover:text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-center text-sm text-slate-600 group-hover:text-slate-800">
                    Voir vos données
                </div>
            </a>
            <a href="{{ route('gdpr.account.download') }}" class="group  w-full sm:w-[120px] xl:w-[160px] flex  flex-col text-decoration-none">
                <div class="rounded-lg h-[75px] sm:h-[90px] xl:h-[120px] bg-gray-50 group-hover:bg-gray-100 flex">
                    <div class="mx-auto my-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 xl:h-12 w-8 xl:w-12 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-center text-sm text-gray-600 group-hover:text-gray-800">
                    Télécharger
                </div>
            </a>
            <a href="{{ route('gdpr.account.unsubscribe') }}" class="group  w-full sm:w-[120px] xl:w-[160px] flex  flex-col text-decoration-none">
                <div class="rounded-lg h-[75px] sm:h-[90px] xl:h-[120px] bg-yellow-50 group-hover:bg-yellow-100 flex">
                    <div class="mx-auto my-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 xl:h-12 w-8 xl:w-12 text-yellow-400 group-hover:text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-center text-sm text-gray-600 group-hover:text-gray-800">
                    Se désinscrire
                </div>
            </a>
            <a href="{{ route('gdpr.account.confirm-delete') }}" class="group  w-full sm:w-[120px] xl:w-[160px] flex  flex-col text-decoration-none">
                <div class="rounded-lg h-[75px] sm:h-[90px] xl:h-[120px] bg-red-50 group-hover:bg-red-100 flex">
                    <div class="mx-auto my-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 xl:h-12 w-8 xl:w-12 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-center text-sm text-gray-600 group-hover:text-gray-800">
                    Supprimer
                </div>
            </a>
        </div>
    </div>

    <div>
        <h1 class="mb-4 text-base flex space-x-2 items-center">
            <span class="font-bold">
                Investigation
            </span>
        </h1>
        <p class="text-sm mb-0">
            Dans le cas où vous demandez des données sur un utilisateur non-lié à un email (avec le nom et le prénom), vous recevrez un email sous 96 heures ouvrées après avoir effectué votre demande d'investigation. Nous nous chargerons de s'assurer que vous êtes bien la personne pour lesquelles vous demandez les données.
        </p>


    </div>

@endsection
