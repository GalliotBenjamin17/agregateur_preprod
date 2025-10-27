@extends('emails.layouts.base2', ['tenant' => $tenant])

@if(sizeof($donationSplits) == 1 && isset($donationSplit) && $donationSplit->project?->name)
    @section('title', "🎉 Merci pour votre contribution au projet " . $donationSplit->project->name . "! 🎉")
@else
    @section('title', "🎉 Merci pour votre contribution ! 🎉")
@endif

@section('content')
    @php
        // La variable $greetingName est maintenant passée directement depuis la route.
    @endphp

    <p style="margin:0;">
        Bonjour{{ isset($greetingName) && $greetingName ? ' ' . $greetingName : '' }},<br><br>

        @if(sizeof($donationSplits) == 1)
            @php
                $certificateUrl = '#'; // Default URL
                $certificateLinkText = 'votre certificat';
                if (isset($donationSplit) && $donationSplit->donation) {
                    $donationInstance = $donationSplit->donation;
                    if (!empty($donationInstance->certificate_pdf_path)) {
                        $certificateUrl = asset($donationInstance->certificate_pdf_path);
                    } else {
                        $certificateFileName = $donationInstance->id . '-' . now()->format('d-m-Y') . '.pdf';
                        $certificateUrl = asset('storage/certificates/donations/' . $certificateFileName);
                    }
                }
            @endphp
            Un grand merci pour votre contribution au projet <strong>{{ $donationSplit->project?->name ?? '' }}</strong> ! Grâce à votre soutien, cette belle initiative pourra voir le jour sur notre territoire. 🌱<br><br>

            Votre certificat de contribution est prêt ! 🏆 Vous pouvez le télécharger dès maintenant en cliquant sur ce lien : <a href="{{ $certificateUrl }}" target="_blank" style="color: {{ $tenant->primary_color ?? '#3B82F6' }}; text-decoration: underline;">{{ $certificateLinkText }}</a>. N'hésitez pas à consulter votre interface contributeur : vous y trouverez les dernières actualités, le suivi du projet ainsi que des photos.<br><br>
        @else
            Un grand merci pour votre contribution  ! Grâce à votre soutien, cette belle initiative pourra voir le jour sur notre territoire. 🌱<br><br>
        @endif
            Si vous avez des questions ou besoin de plus d'informations, nous sommes là pour vous aider. 😊          
            N'hésitez pas à nous contacter !<br><br> 

            Encore un immense merci pour votre engagement à nos côtés, pour construire un territoire durable et résilient, fidèle à nos valeurs communes ! 🌍<br><br>

            À très bientôt,<br>
            L'équipe de la Coopérative Carbone
    </p>
@endsection
