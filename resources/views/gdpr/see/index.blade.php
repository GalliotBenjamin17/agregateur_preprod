@extends('gdpr.layouts.structure')

@section('gdpr-content')
    <div>
        <x-information-alert
            type="info"
            title="Identifiants manquants. "
            message="Si vous n'avez pas d'identifiants d'accès à la plateforme et que vous souhaitez connaître les informations stockées sur vous, envoyez un email au DPO à l'adresse email suivant : eliott.baylot@le-cab-politique.fr. Vous recevrez un email sous 96 heures ouvrées avec vos informations."
        />
        <form method="POST" action="{{ route('gdpr.hub.ask-request', ['key' => 'see']) }}" class="space-y-3 mt-5 grid grid-cols-2">
            @csrf
            <div>
                <x-label value="Email associé au compte : " required />
                <x-input type="email" name="email" placeholder="ex: Renseignez votre email pour voir les données relatives" required />
            </div>
            <x-button type="success" submit icon class="col-span-2 mr-auto">
                <span>Envoyer la demande</span>
                <x-icon.chevron_droite class="h-5 w-5" />
            </x-button>
        </form>
    </div>
@endsection
