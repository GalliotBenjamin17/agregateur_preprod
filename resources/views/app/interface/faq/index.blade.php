<x-app-contributors-2
    :tenant="$tenant"
    :organization="$organization"
>

    <x-contributors-space.section-title
        title="Questions fréquentes"
        class="py-5 font-semibold"
    />

    <div class="bg-white dark:bg-transparent p-4 rounded-lg shadow-xl">
        <div class="space-y-12 px-2 xl:px-5 faq">

            @foreach($tenant->faq ?? [] as $row)
                <div class="faq__item">
                    <div>
                        <div class="faq__item__question"><span class="">Q.</span></div>
                        <div class="faq__item__response flex"><span class="">R.</span></div>
                    </div>
                    <div>
                        <h2 class="faq__title">{{ $row['question'] }}</h2>
                        <div class="faq__content"> {{ $row['answer'] }} </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-contributors-2>
