@extends('gdpr.layouts.structure')

@section('gdpr-content')
    <div>
        <form method="POST" action="{{ route('gdpr.hub.redirect-to-request') }}" class="space-y-3 grid grid-cols-2">
            @csrf
            <div>
                <x-label value="Code reçu sur votre email : " required />
                <x-input type="text" name="code" placeholder="ex: XHgfDPw2tgy3" required />
            </div>
            <x-button type="success" submit icon class="col-span-2 mr-auto">
                <span>Voir les données</span>
                <x-icon.chevron_droite class="h-5 w-5" />
            </x-button>
        </form>
    </div>
@endsection
