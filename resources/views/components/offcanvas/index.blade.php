@props([
	'id' => Str::orderedUuid(),
	'title' => '',
	'size' => 'lg'
])

<div @class([
        "offcanvas offcanvas-end !bg-white !m-3 !rounded-md !overflow-hidden !shadow-2xl",
        "offcanvas-size-sm" => $size == "sm",
        "offcanvas-size-md" => $size == "md",
        "offcanvas-size-lg" => $size == "lg",
        "offcanvas-size-xl" => $size == "xl",
        "offcanvas-size-xxl" => $size == "xxl",
    ])
     tabindex="-1" id="{{ $id }}">
    <div class="offcanvas-header border-b border-gray-300 bg-[#f8f9fd]">
        <h5 class="font-bold text-xl">{{ $title }}</h5>
        <button type="button" class="ml-auto bg-white shadow rounded-md p-1 border border-gray-300" data-bs-dismiss="offcanvas" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    {{ $slot }}

</div>
