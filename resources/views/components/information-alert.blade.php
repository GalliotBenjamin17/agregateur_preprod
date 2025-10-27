@props([
    'type' => 'info',
    'title' => '',
    'message' => ''
])

<div @class([
        'border-l-4 border rounded-md shadow-sm p-4',
        'border-blue-400 bg-blue-50' => $type == 'info',
        'border-green-400 bg-green-50' => $type == 'success',
        'border-yellow-400 bg-yellow-50' => $type == 'warning',
        'border-red-400 bg-red-50' => $type == 'danger',
    ])>
    <div class="flex">
        <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" @class([
                    'h-5 w-5',
                    'text-blue-400' => $type == 'info',
                    'text-green-400' => $type == 'success',
                    'text-yellow-400' => $type == 'warning',
                    'text-red-400' => $type == 'danger',
                ])>

                @switch($type)
                    @case('info')
                        <path fill-rule="evenodd" d="M19 10.5a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0zM8.25 9.75A.75.75 0 019 9h.253a1.75 1.75 0 011.709 2.13l-.46 2.066a.25.25 0 00.245.304H11a.75.75 0 010 1.5h-.253a1.75 1.75 0 01-1.709-2.13l.46-2.066a.25.25 0 00-.245-.304H9a.75.75 0 01-.75-.75zM10 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        @break
                    @case('success')
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        @break
                    @case('warning')
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        @break
                    @case('danger')
                        <path fill-rule="evenodd" d="M10.339 2.237a.532.532 0 00-.678 0 11.947 11.947 0 01-7.078 2.75.5.5 0 00-.479.425A12.11 12.11 0 002 7c0 5.163 3.26 9.564 7.834 11.257a.48.48 0 00.332 0C14.74 16.564 18 12.163 18 7.001c0-.54-.035-1.07-.104-1.59a.5.5 0 00-.48-.425 11.947 11.947 0 01-7.077-2.75zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        @break
                @endswitch
            </svg>
        </div>
        <div class="ml-3">
            <p @class([
                    'text-sm',
                    'text-blue-600' => $type == 'info',
                    'text-green-600' => $type == 'success',
                    'text-yellow-600' => $type == 'warning',
                    'text-red-600' => $type == 'danger',
                ])>
                <span class="font-semibold">{{ $title }}</span>
                <span>{!! $message !!}</span>
            </p>
        </div>
    </div>
</div>
