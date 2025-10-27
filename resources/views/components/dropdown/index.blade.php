@props([
    'dropdownPosition' => 'left' // left, right, center
])

<div x-data="{dropdownOpen: false}" class="relative">
    <button @click="dropdownOpen=true" {{ $attributes->merge(['class' => '']) }}>
        {{ $trigger }}
    </button>

    <div x-show="dropdownOpen"
         @click.away="dropdownOpen=false"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="-translate-y-2"
         x-transition:enter-end="translate-y-0"
         @class([
            "absolute top-0 z-50 w-48 mt-8",
            "-translate-x-1/2 left-1/2" => $dropdownPosition == 'center',
            "right-0" => $dropdownPosition == 'left',
            "left-0" => $dropdownPosition == 'right',
         ])
         x-cloak>
        <div class="p-1 mt-1 bg-white border rounded-md shadow-md border-neutral-200/70 text-neutral-700">

            {{ $slot }}

        </div>
    </div>
</div>
