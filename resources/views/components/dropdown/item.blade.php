@props([
    'subMenu' => null,
])

@if(!$subMenu)
    <a {{ $attributes->merge(['class' => "relative flex select-none hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50"]) }}>

        @isset($icon)
            <div class="w-4 h-4 mr-2">
                {{ $icon }}
            </div>
        @endisset

        <span>{{ $slot }}</span>
    </a>
@else
    <div class="relative group">
        <div {{ $attributes->merge(['class' => "relative flex select-none hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50"]) }}>
            @isset($icon)
                <div class="w-4 h-4 mr-2">
                    {{ $icon }}
                </div>
            @endisset

            <span>{{ $slot }}</span>

            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 ml-auto"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
        <div data-submenu class="absolute top-0 right-0 invisible mr-1 duration-200 ease-out translate-x-full opacity-0 group-hover:mr-0 group-hover:visible group-hover:opacity-100">
            <div class="z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md animate-in slide-in-from-left-1 w-40">

                {{ $subMenu }}

            </div>
        </div>
    </div>
@endif
