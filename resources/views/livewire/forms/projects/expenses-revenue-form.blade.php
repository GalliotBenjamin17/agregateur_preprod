<div>
    <form wire:submit="submit">
        {{ $this->form }}

       <div class="flex justify-end">
           <x-filament::button type="submit" color="success">
               Mettre à jour
           </x-filament::button>
       </div>
    </form>

    <x-layouts.card
        name="Visualisation"
        :thin-padding="true"
        :collapsed="false"
        :collapsible="true"
        class="mt-5"
    >
        <x-slot:content>
            <div class="grid grid-cols-2 gap-10  p-3">
                <livewire:widgets.projects.expenses-chart-widget :project="$project" />
                <livewire:widgets.projects.revenues-chart-widget :project="$project" />
            </div>
        </x-slot:content>
    </x-layouts.card>
</div>
