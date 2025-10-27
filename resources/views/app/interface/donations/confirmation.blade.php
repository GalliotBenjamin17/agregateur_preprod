<x-app-contributors :tenant="$tenant">
    <x-slot:content>
        @section('title', "Confirmation")
        <div class="max-w-[75rem]  mx-auto">
            <div class=" pt-10 text-center">
                <div class="text-[40px] text-[#404040] font-bold ">
                    Merci pour votre contribution
                </div>
                <div class="sub-line mx-auto"></div>
            </div>

            <div class="mt-2">
                <div class="mx-auto py-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="success mx-auto h-56 w-56" viewBox="0 0 101 101" fill="none">
                        <ellipse cx="50.5171" cy="49.9367" rx="11.5213" ry="11.5497" fill="#00AC3E"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" class="checkmark" d="M45.2576 48.604L49.084 52.6483L55.1814 46.1154" stroke="white" stroke-width="2.5"/>
                        <line  class="line1" x1="61.8656" y1="13.4946" x2="58.3004" y2="26.8" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                        <line  class="line2" x1="84.906" y1="32.622" x2="72.9767" y2="39.5094" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                        <line  class="line3" x1="87.6729" y1="62.4366" x2="74.3675" y2="58.8715" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                        <line  class="line4" x1="68.5467" y1="85.4764" x2="61.6593" y2="73.5471" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                        <line  class="line5" x1="45.1935" y1="75.715" x2="41.6283" y2="89.0205" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                        <line  class="line6" x1="29.1204" y1="64.8283" x2="17.1911" y2="71.7157" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                        <line  class="line7" x1="25.4527" y1="45.765" x2="12.1473" y2="42.1998" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                        <line  class="line8" x1="36.3394" y1="29.6917" x2="29.452" y2="17.7624" stroke="#00AC3E" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="bg-gray-50 border border-gray-300 shadow-sm px-4 py-6 sm:rounded-lg sm:p-6 md:flex md:items-center md:justify-between md:space-x-6 lg:space-x-8">
                    <dl class="flex-auto space-y-4 divide-y divide-gray-200 text-sm text-gray-600 md:grid md:grid-cols-3 md:gap-x-6 md:space-y-0 md:divide-y-0 lg:w-1/2 lg:flex-none lg:gap-x-8">

                        <div class="flex justify-between md:block">
                            <dt class="font-semibold text-gray-900">Date</dt>
                            <dd class="md:mt-1">
                                {{ \Carbon\Carbon::userDatetime($donation->created_at, capitalized:true) }}
                            </dd>
                        </div>

                        <div class="flex justify-between pt-4 md:block md:pt-0">
                            <dt class="font-semibold text-gray-900">Montant</dt>
                            <dd class="md:mt-1">{{ format($donation->amount) }} €</dd>
                        </div>

                        <div class="flex justify-between pt-4 text-gray-900 md:block md:pt-0">
                            <dt class="font-semibold text-gray-900">Numéro</dt>
                            <dd class="md:mt-1">{{ $donation->external_id }}</dd>
                        </div>

                    </dl>
                    <div class="mt-6 space-y-4 sm:flex sm:space-x-4 sm:space-y-0 md:mt-0">
                        <x-button href="{{ asset($donation->certificate_pdf_path) }}" target="_blank" icon>
                            <span>Certificat</span>
                            <x-icon.lien_externe class="h-4 w-4" />
                        </x-button>
                    </div>
                </div>

                <div class="mt-20 text-center">
                    <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700 hover:underline">Revenir au tableau de bord</a>
                </div>
            </div>
        </div>

        <script src="{{ asset('js/anime.min.js') }}"></script>
        <script>

            var tl = anime.timeline({
                //easing: 'easeOutExpo',
                duration: 750,
                autoplay: true,
                loop: false
            });

            tl
                .add({
                    targets: '.success',
                    scale:[0.001, 1],
                    rotate:[100,360],
                    opacity: [0.001, 1],
                    easing: 'easeOutExpo',
                    //translateY: 50,
                    duration: 1000

                },)


                .add({
                    targets: '.checkmark',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    scale:[0.001, 1],
                    duration: 500,
                    easing: 'easeInOutSine',

                    strokeDashoffset: [anime.setDashoffset, 0],
                },200)

                .add({
                    targets: '.line1',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.000, 1],
                    duration: 1000

                },400)

                .add({
                    targets: '.line2',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.001, 1],
                    duration: 1000

                },300)

                .add({
                    targets: '.line3',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.001, 1],
                    duration: 1000

                },400)

                .add({
                    targets: '.line4',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.001, 1],
                    duration: 1000

                },400)

                .add({
                    targets: '.line5',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.001, 1],
                    duration: 1000

                },300)

                .add({
                    targets: '.line6',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.001, 1],
                    duration: 1000

                },400)

                .add({
                    targets: '.line7',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.001, 1],
                    duration: 1000

                },300)

                .add({
                    targets: '.line8',
                    transformOrigin: ['50% 50% 0px', '50% 50% 0px'],
                    opacity: {
                        value:[0, 1],
                        delay:50,
                    },
                    scale:[0.001, 1],
                    duration: 1000

                },400)
        </script>
    </x-slot:content>
</x-app-contributors>
