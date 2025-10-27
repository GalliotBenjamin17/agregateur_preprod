@props([
    'stepsCount' => 6
])

<ol @class([
        "grid grid-cols-1 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 overflow-hidden bg-white rounded-lg border border-gray-100 text-sm text-gray-500 sm:items-center",
        "sm:grid-cols-6" => $stepsCount == 6,
        "sm:grid-cols-5" => $stepsCount == 5,
        "sm:grid-cols-4" => $stepsCount == 4,
        "sm:grid-cols-3" => $stepsCount == 3,
])>
    @isset($steps)
        {{ $steps }}
    @endisset
</ol>
