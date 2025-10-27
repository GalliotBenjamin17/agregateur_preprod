@extends('gdpr.layouts.structure')

@section('gdpr-content')
    <div>
        <div class="space-y-2">
            <x-information-alert
                type="info"
                title="Identifiants manquants. "
                message="Si vous n'avez pas d'identifiants d'accès à la plateforme et que vous souhaitez connaître les informations stockées sur vous, envoyez un email au DPO à l'adresse email suivant : XXX@XXX.com. Vous recevrez un email sous 96 heures ouvrées avec vos informations."
            />
        </div>
        <form method="POST" action="{{ route('gdpr.download') }}" class="space-y-5">
            @csrf
            <div>
                <x-label value="Email associée au compte : " required />
                <x-input type="email" name="email" placeholder="ex: Renseignez l'email pour télécharger vos données" required />
            </div>
            <div>
                <x-label value="Mot de passe : " required />
                <x-input type="password" name="password" placeholder="************" required />
            </div>
            <x-button type="success" submit icon class="col-span-2 mr-auto">
                <span>Télécharger vos données</span>
                <x-icon.chevron_droite class="h-5 w-5" />
            </x-button>
        </form>
    </div>
@endsection
