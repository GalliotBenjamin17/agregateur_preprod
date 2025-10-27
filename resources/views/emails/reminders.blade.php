@extends('emails.layouts.base2', ['tenant' => $tenant])

@section('title', $reminder->related?->name ?? "Vous avez un nouveau rappel")

@section('content')
    <p style="margin:0;">
        Bonjour {{ $user->name }},<br><br>

        Vous avez configuré un rappel aujourd'hui sur <span style="font-weight: bold">{{ $reminder->related?->name }} </span>: <br><br>

        <span style="font-style: italic">{{ $reminder->content }}</span>
    </p>
@endsection

@section('action')
    @if($link)
        <x-emails.button
            text="Page détails"
            :bg-color="$tenant?->primary_color"
            :text-color="$tenant?->primary_color_text"
            :link="$link"
        />
    @endif
@endsection
