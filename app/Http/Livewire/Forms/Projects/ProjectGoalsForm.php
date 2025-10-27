<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Enums\Roles;
use App\Helpers\ActivityHelper;
use App\Models\Project;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class ProjectGoalsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Project $project;

    public function mount()
    {
        $this->form->fill([
            'project' => $this->project->toArray(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->disabled(! request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
                ->schema([
                    Fieldset::make('goal')
                        ->label('Objectif principal')
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema([
                            Textarea::make('project.goal_text')
                                ->helperText('Fournissez une description concrète des activités des actions, etc.')
                                ->label('Objectif')
                                ->required()
                                ->placeholder('10 000 tonnes de projets économisés')
                                ->rows(2)
                                ->columnSpanFull(),

                            DatePicker::make('project.start_at')
                                ->placeholder('dd/mm/YYYY')
                                ->required()
                                ->displayFormat('d/m/Y')
                                ->label('Date de démarrage'),

                            TextInput::make('project.duration')
                                ->placeholder('5')
                                ->required()
                                ->label('Durée (années)')
                                ->numeric(),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        $this->project->update($this->form->getState()['project']);

        Notification::make()
            ->title('Objectifs mis à jour.')
            ->body('Les objectifs du projet ont été mis à jour. Rechargez la page des contributions pour voir les modifications.')
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->project,
            title: 'Mise à jour des objectifs du projet',
            url: route('projects.show.goals', ['project' => $this->project])
        );

    }

    public function render()
    {
        return view('livewire.forms.projects.project-goals-form');
    }
}
