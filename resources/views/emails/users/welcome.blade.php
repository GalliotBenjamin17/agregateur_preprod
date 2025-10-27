@extends('emails.layouts.base2', ['tenant' => $tenant])

@section('title', "Configuration de votre compte")

@section('content')
    <p style="margin:0;">
        Bonjour {{ $user->name }},<br><br>

        {!! $content !!}<br><br>

        Bonne journée,<br>
        {{ $tenant?->name }}<br><br>

        @isset($welcomeUrl)
            Si le bouton ne fonctionne pas, cliquez sur ce lien : <a href="{{ $welcomeUrl }}">Lien de configuration de compte</a>
        @endisset
    </p>
@endsection

@section('action')
    @isset($welcomeUrl)
        <x-emails.button
            text="Configurez votre compte"
            :bg-color="$tenant->primary_color"
            :text-color="$tenant->primary_color_text"
            :link="$welcomeUrl"
        />
    @else
        @if($tenant)
            <x-emails.button
                text="Connexion"
                :bg-color="$tenant->primary_color"
                :text-color="$tenant->primary_color_text"
                :link="route('tenant.dashboard', ['tenant' => $tenant])"
            />
        @else
            <x-emails.button
                text="Connexion"
                :link="route('dashboard')"
            />
        @endif
    @endisset
@endsection
