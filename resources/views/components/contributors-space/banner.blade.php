@props([
    'title',
    'description',
    'buttonText',
    'buttonUrl' => "#",
    'backgroundImage',
    'backgroundPosition' => 'top'
])

<div @class([
    "rounded-2xl bg-cover",
    "bg-bottom" => $backgroundPosition == "bottom",
    "bg-top" => $backgroundPosition == "top",
]) style="background-image: url('{{ $backgroundImage }}');">
    <div {{ $attributes->class(['rounded-2xl py-12 px-6 lg:px-12 text-center mt-6 mb-6 backdrop-blur-sm bg-gray/30 text-black']) }} >
        <div>
            <h1 class="text-3xl font-semibold mb-5">
                {{ $title }}
            </h1>
            <div class="pb-6">
                {!! $description !!}
            </div>
            <div>
                <a class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded-full border-white ring-gray-200 dark:ring-gray-500 bg-white text-black hover:bg-gray-100 py-2 px-6" href="{{ $buttonUrl }}" disabled="false">

                    <span class="px-2">{{ $buttonText }}</span>

                    <x-icon.arrow-rightt-circle class="w-5 h-5" />
                </a>
            </div>
        </div>
    </div>

</div>
