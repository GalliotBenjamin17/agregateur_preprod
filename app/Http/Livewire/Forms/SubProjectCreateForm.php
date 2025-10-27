<?php

namespace App\Http\Livewire\Forms;

use App\Models\Project;
use App\Models\ProjectCarbonPrice;
use App\Traits\Filament\HasDataState;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;

class SubProjectCreateForm extends Component implements HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }

    public Project $project;

    public function mount()
    {
        $this->form->fill([
            'type' => null,
            'name' => null,
            'description' => null,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('name')
                ->label('Dénomination & explications')
                ->schema([
                    Select::make('type')
                        ->lazy()
                        ->label('Type de sous-projet')
                        ->columnSpanFull()
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->options(config('values.sub_projects.types')),

                    // Geographical objective
                    TextInput::make('name')
                        ->label('Nom du sous-projet')
                        ->lazy()
                        ->visible(fn (\Filament\Forms\Get $get) => $get('type') == 'geographical_objective')
                        ->required(fn (\Filament\Forms\Get $get) => $get('type') == 'geographical_objective')
                        ->columnSpanFull(),

                    RichEditor::make('description')
                        ->helperText('Fournissez une description concrète des activités, des actions, etc.')
                        ->label('Description du sous-projet')
                        ->toolbarButtons(['codeBlock', 'strike'])
                        ->visible(fn (\Filament\Forms\Get $get) => $get('type') == 'geographical_objective')
                        ->columnSpanFull(),

                    // Annual objective
                    TextInput::make('sub_project_year')
                        ->label('Année du sous-projet')
                        ->integer()
                        ->lazy()
                        ->visible(fn (\Filament\Forms\Get $get) => $get('type') == 'temporal_objective')
                        ->required(fn (\Filament\Forms\Get $get) => $get('type') == 'temporal_objective')
                        ->columnSpanFull(),

                ]),

            Fieldset::make('address')
                ->label('Adresse du sous-projet')
                ->visible(fn (\Filament\Forms\Get $get) => $get('type') == 'geographical_objective')
                ->schema([
                    TextInput::make('address_1')
                        ->label('Adresse 1')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid())
                        ->placeholder('Forêt de ...')
                        ->autofocus(),
                    TextInput::make('address_2')
                        ->label('Complément')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid())
                        ->placeholder('xx chemin de la forêt'),
                    TextInput::make('address_postal_code')
                        ->label('Code postal')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid())
                        ->placeholder('69006'),
                    TextInput::make('address_city')
                        ->label('Ville')
                        ->lazy()
                        ->autocomplete(Str::orderedUuid())
                        ->placeholder('Lyon'),
                ]),
        ];
    }

    public function submit()
    {
        $state = $this->form->getState();

        $newProject = Project::create([
            'name' => $state['name'] ?? $this->project->name.' - '.$state['sub_project_year'],
            'tenant_id' => $this->project->tenant->id,
            'parent_project_id' => $this->project->id,
            'sub_project_year' => Arr::get($state, 'sub_project_year', null),
            'sponsor_type' => $this->project->sponsor_type,
            'sponsor_id' => $this->project->sponsor_id,
            'created_by' => request()->user()->id,
            'description' => $state['description'] ?? null,
            'address_1' => $state['address_1'] ?? null,
            'address_2' => $state['address_2'] ?? null,
            'address_city' => $state['address_city'] ?? null,
            'address_postal_code' => $state['address_postal_code'] ?? null,
            'referent_id' => $this->project->referent_id,
            'is_synchronized_with_parent' => true,
        ]);

        $newProject->auditors()->sync($this->project->auditors);

        $newActiveCarbonPrice = ProjectCarbonPrice::create([
            'price' => $this->project->activeCarbonPrice->price,
            'created_by' => request()->user()->id,
            'project_id' => $newProject->id,
            'sync_with_tenant' => true,
            'start_at' => now(),
        ]);

        Session::flash('success', 'Le sous-projet a été ajouté au projet.');

        return redirect()->route('projects.show.goals', ['project' => $this->project->slug]);
    }

    public function render()
    {
        return view('livewire.forms.sub-project-create-form');
    }
}
