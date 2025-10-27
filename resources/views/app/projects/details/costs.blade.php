<x-pages.projects.details-base
    :project="$project"
>
    <x-slot name="fullContent">
        <style>
            .filament-tables-table-container {
                border-top-left-radius: 0px !important;
                border-top-right-radius: 0px !important;
            }

            .fi-section-header {
                padding: 10px !important;
            }
        </style>

        <div class="space-y-5">



            <x-layouts.card
                name="Informations de financement"
                :thin-padding="true"
                :collapsed="false"
                :collapsible="true"
            >
                <x-slot:content>
                    <div class="p-3">
                        <livewire:forms.projects.project-costs-form :project="$project" />
                    </div>
                </x-slot:content>
            </x-layouts.card>


            <x-layouts.card
                name="Paiements initiés au porteur"
                :thin-padding="true"
                :collapsed="true"
                :collapsible="true"
            >
                <x-slot:actions>
                    @role('admin|local_admin')
                        <livewire:actions.project-holder-payments.create-form :project="$project" />
                    @endrole
                </x-slot:actions>

                <x-slot:content>
                    <livewire:tables.project-holder-payment.index-table :project="$project" />
                </x-slot:content>
            </x-layouts.card>

            <x-layouts.card
                name="Paiements initiés aux partenaires"
                :thin-padding="true"
                :collapsed="true"
                :collapsible="true"
            >

                <x-slot:actions>
                    <x-filament::button tag="a" :href="route('projects.show.partners', ['project' => $project])" size="xs" >
                        Ajouter un paiement
                    </x-filament::button>
                </x-slot:actions>


                <x-slot:content>
                    <livewire:tables.partner-project-payments.index-table :project="$project" />
                </x-slot:content>
            </x-layouts.card>


            @if($project->can_be_financed_online and $project->hasChildrenProjects())
                <x-information-alert
                    type="warning"
                    :message="'Seul le projet parent est affiché et finançable sur le site public. Ainsi, le prix à la tonne affiché et retenu pour les contributions sera de ' . $project->activeCarbonPrice?->price . ' € HT.'"
                    title="Information sur le prix affiché de la tonne."
                />
            @endif

            <x-layouts.card
                group-name="Projets"
                name="Prix par tonne CO2"
                :thin-padding="false"
                :collapsed="true"
                :collapsible="true"
            >
                <x-slot:icon>
                    {!! \App\Helpers\IconHelper::donationsIcon() !!}
                </x-slot:icon>

                <x-slot:actions>
                    @if(!$project->hasParent())
                        @role('admin|local_admin')
                            <x-button data-bs-toggle="modal" data-bs-target="#add_carbon_price">
                                Définir un nouveau prix
                            </x-button>

                            <x-modal id="add_carbon_price">
                                <x-modal.header>
                                    <div>
                                        <div class="font-semibold text-gray-700">
                                            Ajout d'un nouveau prix
                                        </div>
                                    </div>
                                    <x-modal.close-button/>
                                </x-modal.header>

                                <form autocomplete="off" action="{{ route('project-carbon-price.store', ['project' => $project]) }}" method="POST" enctype='multipart/form-data'>
                                    @csrf
                                    <x-modal.body x-data="{reSync : false}" class="grid grid-cols-1 sm:grid-cols-1 gap-4 border-t border-gray-300">
                                        <div>
                                            <x-information-alert
                                                type="warning"
                                                title="Changement de prix"
                                                message="Cette action créera un nouveau prix et désactivera le précédent automatiquement. Ce prix ne sera plus synchronisé sur l'instance locale."
                                            />
                                        </div>

                                        <x-checkbox
                                            content="Resynchroniser le prix sur celui de l'instance."
                                            name="is_sync"
                                            x-on:click="reSync = !reSync"
                                            class="w-full" />

                                        <div x-show="!reSync">
                                            <x-label value="Nouveau prix (HT): " required />
                                            <x-input type="number" x-bind:required="!reSync" name="price" step=".01" placeholder="45" required />
                                        </div>
                                    </x-modal.body>

                                    <x-modal.footer>
                                        <x-button data-bs-dismiss="modal">
                                            Fermer
                                        </x-button>
                                        <x-button submit type="success">
                                            Ajouter
                                        </x-button>
                                    </x-modal.footer>
                                </form>
                            </x-modal>
                        @endrole
                    @else
                        <div class="border-l-4 border-yellow-400 bg-yellow-50 px-4 py-2 rounded-md">
                            <p class="text-sm text-yellow-700">
                                Le prix est synchronisé avec celui du projet parent.
                            </p>
                        </div>
                    @endif
                </x-slot:actions>

                <x-slot:content>
                    <livewire:tables.projects.project-donation-carbon-price-table :project="$project" />
                </x-slot:content>
            </x-layouts.card>

            <x-layouts.card
                name="Dépenses / Recettes"
                :thin-padding="true"
                :collapsed="true"
                :collapsible="true"
            >
                <x-slot:content>
                    <div class="p-3">
                        <livewire:forms.projects.expenses-revenue-form :project="$project" />
                    </div>
                </x-slot:content>
            </x-layouts.card>
        </div>
    </x-slot>
</x-pages.projects.details-base>
