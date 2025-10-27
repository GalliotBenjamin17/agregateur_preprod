@props([
    'title',
    'number'
])

<div class="rounded-2xl flex-col dark:bg-slate-900/70 bg-white flex">
    <div class="flex-1 p-6">
        <div {{ $attributes->class(['justify-between items-center flex']) }}>
            <div class="flex items-center justify-center">
                <div>
                    <h3 class="text-lg leading-tight text-gray-500 dark:text-slate-400">
                        {{ $title }}
                    </h3>
                    <h1 class="text-3xl leading-tight font-semibold">
                        {{ $number }}
                    </h1>
                </div>
            </div>
            @isset($icon)
                <div>
                    {{ $icon }}
                </div>
            @endisset
        </div>

    </div>
    <!---->
</div>
