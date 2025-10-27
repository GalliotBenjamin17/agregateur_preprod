@extends('emails.layouts.base2', ['tenant' => $tenant])

@section('title', $tenant?->name . " - Réglez votre paiement en ligne")

@section('content')

    <p style="margin:0;">
        @if($text)
            {!! $text !!}
        @else
            Bonjour,<br><br>

            Une personne a généré un lien de paiement pour que vous puissiez régler le montant de votre contribution directement en ligne.<br><br>
            Cliquez sur le bouton ci-dessous pour régler en ligne.
        @endif
        <br><br>

        Bonne journée,<br>
        {{ $tenant->name }}<br><br>
    </p>
@endsection

@section('action')
    <x-emails.button
        text="Page de paiement"
        :bg-color="$tenant->primary_color"
        :text-color="$tenant->primary_color_text"
        :link="$link"
    />
@endsection
