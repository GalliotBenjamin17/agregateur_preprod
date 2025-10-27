@php
    $record = $getRecord();
@endphp

<div class="flex items-center gap-1 space-x-2 px-4 py-2">
    @if($record['related'])
        <span>{!! \App\Helpers\IconHelper::viewIcon(model: $record['related'], size: 'xs') !!}</span>
        <span>{{ $record['related']['name'] ?? 'Aucun' }}</span>
    @else
        <span>Fichier global</span>
    @endif
</div>
