@extends('emails.layouts.base2', ['tenant' => $tenant])

@section('title', "Code de vérification RGPD")

@section('content')
    <p style="margin:0;">
        Bonjour,<br><br>

        Vous avez récemment fait une demande pour accéder à une section du hub RGPD. Vous trouverez ci-dessous le code à renseigner dans la page sur laquelle vous avez renseignée votre email.
        <br><br>

        <ul style="list-style:none;padding-left:0;margin-top:0;margin-bottom:0">
            <li>
                <span style="font-weight:bold">Code&nbsp;:</span> {{ $gdprRequest->code }}
            </li>
            <li>
                <span style="font-weight:bold">Expiration&nbsp;:</span> {{ $gdprRequest->expires_at->format('d/m/Y H:i:s') }}
            </li>
        </ul><br><br>

        Bonne journée,<br>
        @isset($tenant)
            {{ $tenant->name }} <br><br>
        @endisset

    </p>
@endsection
