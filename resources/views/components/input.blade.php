@props([
    'class' => false,
    'id' => false,
    'type' => 'text',
    'name' => false,
    'error' => false,
    'size' => 'md',
    'disabled' => false
])

<div @if($class) class="{{ $class }}" @endif>
    <div class="flex rounded-md shadow-sm">
        <input type="{{ $type }}"
               @if($name) name="{{ $name }}" @endif
               @if($id) id="{{ $id }}" @endif
               @if($disabled) disabled @endif
               {{ $attributes }}
               class="placeholder-blue-gray-400 text-blue-gray-900 mt-1
                @if($error)
                    border-red-400 focus:border-red-500 focus:ring-red-200
                @else
                    border-gray-300 focus:border-blue-500 focus:ring-blue-200
                @endif
                @if($disabled)
                    bg-gray-100 cursor-not-allowed
                @endif
                @if($size == 'sm')
                    h-8 py-1 px-2
                @else
                    py-2 px-3
                @endif
                focus:ring-2 block w-full rounded-md sm:text-sm"
        />
    </div>
</div>
