@php
    $tags = $getState();
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class([
    'filament-tables-tags-column flex items-center gap-1 px-4 py-3',
]) }}>
    @forelse($tags as $tag)
        <span class="inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl text-primary-700 bg-primary-500/10 whitespace-normal">
            {{ $tag['name']}}
        </span>
    @empty
        <span class="italic text-sm">Aucune étiquette</span>
    @endforelse
</div>
