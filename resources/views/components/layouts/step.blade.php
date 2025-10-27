@props([
    'title',
    'done' => false,
    'pending' => false,
])

<li {{ $attributes->class([
        'flex items-center justify-center p-4 tippy',
        'bg-[#014486] text-white' => $done and !$pending,
        'bg-success !text-white' => $pending,
        'bg-white text-gray-600' => !$done,
    ]) }} data-tippy-content="{{ $pending ? 'En cours' : ($done ? 'Terminé' : 'A venir') }}">

    @isset($icon)
        {!! $icon !!}
    @endisset

    <p class="block pt-1 font-medium truncate"> {{ $title }} </p>
</li>
