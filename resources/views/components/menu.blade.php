@props([
    "key"
])

<div {{ $attributes->class(['bg-white mt-1 px-6 pb-3 md:pb-0 border-b-2 border-slate-600 flex items-center']) }}>
    <div class="flex items-center space-x-3">

        @isset($shortcut)
            {{ $shortcut }}
        @endisset

        <p class="hidden md:block text-[#181818]">
            {{ $key }}
        </p>
    </div>

    <div class="hidden sm:flex sm:items-end ml-5">
        {{ $slot }}
    </div>
</div>
