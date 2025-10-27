@php
    $record = $getRecord();
@endphp

<div class="flex flex-wrap items-center gap-1 space-x-2 px-4 py-2">
    <img class="h-5 hidden sm:block" src="/img/extensions/{{ $record['extension'] }}.png" onerror="this.onerror=null;this.src='{{ asset('img/extensions/doc.png') }}';">
    <span>{{ \Illuminate\Support\Str::limit($record['name'], 50) }}</span>
</div>
