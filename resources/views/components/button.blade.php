@props([
    'class' => '',
    'href' => null,
    'submit' => null,
    'icon' => false,
    'active' => false
])

<@if($href)
a href="{{ $href }}"
@elseif($submit)
button type="submit"
@else
span
@endisset
    class="button rounded-md shadow-md border cursor-pointer transition-opacity focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
        @if($icon)
            flex flex-row items-center justify-center space-x-2
        @else
            inline-block text-center
        @endif
        @switch($size)
            @case(\App\View\Components\Button::SIZE_SM)
                h-8 py-1 px-3 leading-6 text-sm
                @break
            @case(\App\View\Components\Button::SIZE_MD)
                @break
            @case(\App\View\Components\Button::SIZE_LG)
                h-10 py-2 px-4 leading-6 text-base
                @break
        @endswitch
        @switch($type)
            @case(\App\View\Components\Button::TYPE_DEFAULT)
                border-gray-300 bg-gray-50 hover:sm:bg-gray-100 text-gray-700 hover:text-gray-900
                @break
            @case(\App\View\Components\Button::TYPE_INFO)
                border-gh bg-blue-500 hover:bg-blue-600 text-white hover:text-white
                @break
            @case(\App\View\Components\Button::TYPE_SUCCESS)
                border-gh bg-success text-white hover:text-white focus-visible:outline-green-600
                @break
            @case(\App\View\Components\Button::TYPE_DANGER)
                @if($active)
                    border-gh bg-red-600 opacity-90 hover:opacity-100 hover:bg-red-600 text-white hover:text-white
                @else
                    border-gh bg-gray-50 hover:bg-red-600 text-red-700 hover:text-white
                @endif
                @break
            @case(\App\View\Components\Button::TYPE_DANGEROUS)
                @if($active)
                    border-gh bg-red-600 opacity-90 hover:opacity-100 hover:bg-red-600 text-white hover:text-white
                @else
                    border-gh bg-red-600 hover:bg-red-700 text-white hover:text-white
                @endif
                @break
            @case(\App\View\Components\Button::TYPE_WARNING)
                @if($active)
                    border-gh bg-yellow-500 opacity-90 hover:opacity-100 hover:bg-yellow-500 text-white hover:text-white
                @else
                    border-gh bg-gray-50 hover:bg-yellow-500 text-yellow-600 hover:text-white
                @endif
                @break
        @endswitch
        {{ $class }}"
    style="--tw-shadow: 0 1px 3px 0 rgba(143, 143, 143, 0.2), 0 -2.4px 0 inset rgba(62, 62, 62, 0.04);"
    {{ $attributes }}
>
     {{ $slot }}
</@if($href)
a
@elseif($submit or $type == 'submit')
button
@else
span
@endif>
