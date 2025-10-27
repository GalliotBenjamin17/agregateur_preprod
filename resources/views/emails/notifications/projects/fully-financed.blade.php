@extends('emails.layouts.base2', ['tenant' => $tenant])

@section('title', $tenant?->name . " - Financement du projet atteint")

@section('content')

    <p style="margin:0;">

        Bonjour,<br><br>

        Le projet {{ $project->name }}, auquel vous avez contribué, a atteint 100% de son financement.<br><br>

        Merci encore pour votre soutien !<br><br>

        Pour rappel, vous pouvez télécharger votre certificat de contribution depuis votre profil et suivre les actualités du projet.<br><br>


        Bonne journée,<br>
    </p>
@endsection

@section('action')
    <x-emails.button
        text="Page de connexion"
        :bg-color="$tenant->primary_color"
        :text-color="$tenant->primary_color_text"
        :link="'https://' . $tenant->domain  . '.' . config('app.displayed_url') . '/login'"
    />
@endsection
