@extends('gdpr.layouts.structure')

@section('title', 'Supprimer mon compte')

@section('gdpr-content')

    <div>
        <h1 class="text-xl font-bold">
            Suppression des données personnelles
        </h1>
        <div class="text-red-500 text-sm flex items-center space-x-2 mt-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
            </svg>
            <span>
                <strong>Attention</strong>, cette opération est irréversible !
            </span>
        </div>
    </div>

    <form action="{{ route('gdpr.account.delete') }}" method="POST" class="space-y-6">
        <div class="form-group">
            <x-label name="email">
                Adresse email
            </x-label>

            <x-input name="email" placeholder="jean.potierexemple.com" autocomplete="email"/>
            @if(!$errors->has('email'))
                <p class="text-muted text-xs p-1">
                    Il s'agit de l'email que vous utilisez pour vous connecter.
                </p>
            @endif
        </div>

        <div class="form-group">
            <x-label name="password">
                Mot de passe
            </x-label>
            <x-input name="password" placeholder="*********" type="password" autocomplete="password"/>
        </div>

        @csrf
        @method('DELETE')

        <div class="space-y-2">
            <x-button submit type="dangerous">
                Supprimer mes informations personnelles
            </x-button>

            <x-button href="javascript:history.back()">
                Conserver mon compte
            </x-button>
        </div>
    </form>
@endsection

@push('scripts')
    @livewireScripts
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/popper.js/umd/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/theme.min.js') }}"></script>
@endpush
