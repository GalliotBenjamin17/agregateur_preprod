@props([
    'class' => false,
    'id' => false,
    'name' => false,
    'error' => false,
    'size' => 'md',
    'disabled' => false,
])

<div @if($class) class="{{ $class }}" @endif>
    <div class="flex rounded-md shadow-sm">
        <select @if($name) name="{{ $name }}" @endif
               @if($id) id="{{ $id }}" @endif
               @if($disabled) disabled @endif
               {{ $attributes }}
               class="
                @if($error)
                    placeholder-red-300 text-red-600 border-red-400 focus:border-red-500 focus:ring-red-200
                @else
                    placeholder-blue-gray-400 text-blue-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-200
                @endif

                @if($disabled)
                    bg-gray-100 cursor-not-allowed
                @endif

                @switch($size)
                    @case(\App\View\Components\Button::SIZE_SM)
                        h-8 py-1 leading-6 text-sm
                        @break
                    @case(\App\View\Components\Button::SIZE_MD)
                        py-2 text-sm
                        @break
                    @case(\App\View\Components\Button::SIZE_LG)
                        h-10 py-2 leading-6 text-base
                        @break
                @endswitch
                    focus:ring-2 block w-full rounded-md mt-1"
        >
            {{ $slot }}
        </select>
    </div>
</div>
