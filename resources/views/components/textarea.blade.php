@props([
    'class' => false,
    'style' => '',
    'id' => false,
    'type' => false,
    'name' => false,
    'error' => false,
    'borderless' => false,
    'disabled' => false
])

<div @if($class) class="{{ $class }}" @endif>
    <div class="flex @if(!$borderless) rounded-md shadow-sm @endif">
        <textarea
               @if($name) name="{{ $name }}" @endif
               @if($id) id="{{ $id }}" @endif
               @if($disabled) disabled @endif
               {{ $attributes }}
               style="min-height: 80px; {{ $style }}"
               class="placeholder-blue-gray-400 text-blue-gray-900 sm:text-sm focus:ring-2 block w-full mt-1 @if(!$borderless) rounded-md @endif py-2 px-4
        @if($error)
           @if($borderless) border-none @else border-red-400 focus:border-red-500 @endif focus:ring-red-200
        @else
           @if($borderless) border-none @else border-gray-300 focus:border-blue-500 @endif focus:ring-blue-200
        @endif
        @if($disabled) bg-gray-100 cursor-not-allowed @endif
        ">{{ $slot }}</textarea>
    </div>
</div>
