@extends('emails.layouts.base2')

@section('title', "Coopérative Carbone - Migration de votre compte")

@section('content')
    <p style="margin:0;">
        Bonjour {{ $user->name }},<br><br>

        La Coopérative Carbone de la Rochelle migre ses données vers une nouvelle plateforme plus adaptée pour les contributeurs et le suivi des projets financés.<br><br>

        Pour définir votre nouveau mot de passe et accéder à votre interface, cliquez sur le bouton ci-dessous.

        Bonne journée,<br>
        Toute l'équipe de la Coopérative Carbone
    </p>
@endsection

@section('action')
    <x-emails.button
        text="Définir mon mot de passe"
        :link="$welcomeUrl"
    />
@endsection
