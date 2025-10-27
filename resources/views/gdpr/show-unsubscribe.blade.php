@extends('gdpr.layouts.structure')

@section('title', 'Désinscription aux email et SMS')

@section('gdpr-content')
    <div>
        <h1 class="text-xl font-bold">
            Désinscription aux email et SMS
        </h1>
    </div>
    @if(Session::has('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        {{ Session::get('success') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <form action="{{ route('gdpr.account.unsubscribe.store') }}" method="POST" class="space-y-4">
            <div class="form-group">
                <x-label value="Adresse email" />

                <x-input name="email" placeholder="jean.potierexemple.com" autocomplete="email"/>
                @if(!$errors->has('email'))
                    <p class="text-muted text-xs p-1">
                        Il s'agit de l'email sur lequel vous ne voulez plus recevoir d'informations.
                    </p>
                @endif
            </div>

            <div class="form-group">
                <x-label value="Téléphone" />

                <x-input x-data="{}" x-mask="99.99.99.99.99" name="phone" placeholder="06 37 74 73 82" autocomplete="phone"/>
                @if(!$errors->has('phone'))
                    <p class="text-muted text-xs p-1">
                        Il s'agit du téléphone sur lequel vous ne voulez plus recevoir d'informations.
                    </p>
                @endif
            </div>

            <div class="form-group">
                <x-label value="Pourquoi ?" name="request_why" />

                <x-textarea name="request_why" placeholder="Je n'ai ..."></x-textarea>
                @if(!$errors->has('request_why'))
                    <p class="text-muted text-xs p-1">
                        Expliquez-nous la raison de cette désinscription.
                    </p>
                @endif
            </div>

            @csrf

            <div class="space-y-2">
                <x-button submit type="warning" class="w-full" size="lg">
                    Me désinscrire
                </x-button>
            </div>
        </form>
    @endif
@endsection
