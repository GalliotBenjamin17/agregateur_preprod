@props([
    'status' => null,
])
            <svg   viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
<path d="M19 13C19 9.231 19 5.417 19 5C19 4.47 18.789 3.961 18.414 3.586C18.039 3.211 17.53 3 17 3C15.014 3 8.986 3 7 3C6.47 3 5.961 3.211 5.586 3.586C5.211 3.961 5 4.47 5 5C5 7.66101 5 16.339 5 19C5 19.53 5.211 20.039 5.586 20.414C5.961 20.789 6.47 21 7 21C7.397 21 8.938 21 12 21" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="1.5" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M8.99896 9.99976L10.999 11.9998L14.999 7.99976" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="1.5" stroke-linecap="round" stroke-linejoin="round"/>
<circle @class(['@text-red-600' => $status === 'error', '@text-amber-500' => $status === 'warning', '@text-emerald-500' => $status === 'ok']) cx="17.5" cy="18.5" r="3.5" fill="currentColor"/>
<path d="M8.99896 17.0003L12 17.0004" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
