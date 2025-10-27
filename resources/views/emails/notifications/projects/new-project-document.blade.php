@extends('emails.layouts.base2', ['tenant' => $tenant])

@section('title', $tenant?->name . " - Nouveau document ajouté au projet")

@section('content')

    <p style="margin:0;">

        Bonjour {{ $notifiable->first_name }},<br><br>

        @if (count($newDocumentTitles) === 1)
            Le document suivant a été ajouté au projet <strong>{{ $project->name }}</strong> auquel vous avez contribué :
        @else
            Les documents suivants ont été ajoutés au projet <strong>{{ $project->name }}</strong> auquel vous avez contribué :
        @endif
    </p>

    <ul>
        @foreach ($newDocumentTitles as $title)
            <li>{{ $title }}</li>
        @endforeach
    </ul>

    <p style="margin:0;">
        Vous pouvez consulter ces nouveautés en vous connectant à votre espace.<br><br>

        Merci encore pour votre soutien !<br><br>

        Bonne journée,<br>
        {{ $tenant->name }}<br><br>
    </p>
@endsection

@section('action')
    <x-emails.button
        text="Accéder au projet"
        :bg-color="$tenant->primary_color"
        :text-color="$tenant->primary_color_text"
        :link="'https://' . $tenant->domain  . '.' . config('app.displayed_url') . '/login'" {{-- Ou un lien plus spécifique ? --}}
    />
@endsection
