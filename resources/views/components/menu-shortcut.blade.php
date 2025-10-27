@props([
    "modalId",
    'text' => "Menu"
])

<a href="#!" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}" class="group flex items-center space-x-2 text-[18px] text-[#181818]">
    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 h-5 w-5" width="24" height="24" viewBox="0 0 24 24"
         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <circle class="duration-1000 group-hover:text-blue-500" cx="5" cy="5" r="1"></circle>
        <circle class="duration-150 group-hover:text-yellow-500" cx="12" cy="5" r="1"></circle>
        <circle class="duration-1000 group-hover:text-green-500" cx="19" cy="5" r="1"></circle>
        <circle class="duration-500 group-hover:text-rose-500" cx="5" cy="12" r="1"></circle>
        <circle class="duration-1000 group-hover:text-blue-500" cx="12" cy="12" r="1"></circle>
        <circle class="duration-150 group-hover:text-yellow-500" cx="19" cy="12" r="1"></circle>
        <circle class="duration-500 group-hover:text-green-500" cx="5" cy="19" r="1"></circle>
        <circle class="duration-100 group-hover:text-rose-500" cx="12" cy="19" r="1"></circle>
        <circle class="duration-1000 group-hover:text-blue-500" cx="19" cy="19" r="1"></circle>
    </svg>
</a>
