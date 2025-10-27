@extends('emails.layouts.base2', ['tenant' => $tenant])

@section('title', $tenant->name.' - Nouvelle actualité sur le projet ' . $project->name)

@section('content')

    <div style="margin:0;">
        Bonjour,<br><br>

        Nouvelle actualité sur le projet {{ $project->name }}, auquel vous avez contribué :<br><br>

        {!! $news->content !!}
        <br><br>

        Pour rappel, vous pouvez télécharger votre certificat de contribution depuis votre profil et suivre les actualités du projet.<br><br>

        Bonne journée,<br>
    </div>
@endsection
