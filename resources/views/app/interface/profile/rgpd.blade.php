<x-app-contributors-2
    :tenant="$tenant"
>

    <div class="space-y-10">

        <x-contributors-space.section-title
            title="Mes données"
            class="py-5 font-semibold "
        />

        <livewire:interface.forms.profile.rgpd-data-form />


        <x-contributors-space.card>

            <x-contributors-space.section-title
                title="Suppression compte"
                class="font-semibold text-red-500"
                size="sm"
            />

            <div class="mb-6 text-justify">
                <p>Effectuez une demande de suppression de compte à cette adresse en précisant vos coordonnées : </p>

                <p class="my-3 hover:underline"><a href="mailto:{{ $tenant->dpo_email ?? "#" }}">{{ $tenant->dpo_email ?? "Aucun email renseigné." }}</a></p>

            </div>

        </x-contributors-space.card>

        <x-contributors-space.card>

            <x-contributors-space.section-title
                title="Questions relatives au RGPD"
                class=" font-semibold "
                size="sm"
            />

            <div class="mb-6 text-justify">
                <p>Si vous avez des questions ou des demandes concernant le traitement de vos données personnelles conformément au Règlement Général sur la Protection des Données (RGPD), veuillez nous contacter à l'adresse e-mail suivante : </p>

                <p class="my-3 text-blue-800 font-bold"><a href="mailto:{{ $tenant->dpo_email ?? "#" }}">{{ $tenant->dpo_email ?? "Aucun email renseigné." }}</a></p>


                <p>Notre délégué à la protection des données (DPO) est disponible pour répondre à toutes vos préoccupations relatives à la confidentialité et à la sécurité de vos données personnelles. Nous nous engageons à traiter toutes les demandes dans les meilleurs délais et à garantir le respect de vos droits en matière de protection des données.</p>
            </div>

        </x-contributors-space.card>

    </div>
</x-app-contributors-2>
