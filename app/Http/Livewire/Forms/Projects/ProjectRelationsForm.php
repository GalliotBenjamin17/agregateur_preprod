<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Enums\Roles;
use App\Helpers\ActivityHelper;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ProjectRelationsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Project $project;

    public array $auditors = [];

    public array $referents = [];

    public function mount()
    {
        $this->auditors = User::role(Roles::Auditor)->get()->pluck('name', 'id')->toArray();
        $this->referents = User::role(Roles::Referent)->get()->pluck('name', 'id')->toArray();

        $this->form->fill([
            'project' => $this->project->toArray(),
            'Organization_sponsor_id' => $this->project->sponsor_id,
            'User_sponsor_id' => $this->project->sponsor_id,
            'auditors' => $this->project->auditors()->pluck('id'),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->disabled(! request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
                ->schema([

                    Toggle::make('project.is_synchronized_with_parent')
                        ->reactive()
                        ->visible($this->project->hasParent())
                        ->label("Est synchronisé avec le projet parent"),

                    Fieldset::make('auditors_referent')
                        ->label('Auditeur et référent')
                        ->schema([
                            Select::make('auditors')
                                ->label('Auditeurs')
                                ->helperText('Seul les utilisateurs ayant le rôle auditeur sont affichés.')
                                ->searchable()
                                ->columnSpanFull()
                                ->preload()
                                ->multiple()
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name} - {$record->email}")
                                ->relationship('auditors', 'first_name', function ($query) {
                                    return $query->where('tenant_id', $this->project->tenant_id)
                                        ->role(Roles::Auditor);
                                })
                                ->disabled(function (Get $get) {
                                    return $get('project.is_synchronized_with_parent');
                                }),

                            Select::make('project.referent_id')
                                ->label('Referent')
                                ->helperText('Seul les utilisateurs ayant le rôle référent sont affichés.')
                                ->searchable()
                                ->disabled(function (Get $get) {
                                    return $get('project.is_synchronized_with_parent');
                                })
                                ->options($this->referents),

                        ]),

                    Fieldset::make('sponsor')
                        ->label('Porteur du projet')
                        ->disabled($this->project->hasFormFieldsDisabled())
                        ->schema([
                            Select::make('project.sponsor_type')
                                ->label('Type de porteur')
                                ->options([
                                    Organization::class => 'Organisation',
                                    User::class => 'Particulier',
                                ])
                                ->searchable()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $set('Organization_sponsor_id', null);
                                    $set('User_sponsor_id', null);
                                })
                                ->required(fn (Get $get) => !$get('project.is_synchronized_with_parent'))
                                ->reactive()
                                ->disabled(function (Get $get) {
                                    return $get('project.is_synchronized_with_parent');
                                }),

                            Select::make('Organization_sponsor_id')
                                ->label('Organisation')
                                ->searchable()
                                ->visible(fn (Get $get) => $get('project.sponsor_type') == Organization::class)
                                ->required(fn (Get $get) => $get('project.sponsor_type') == Organization::class and !$get('project.is_synchronized_with_parent'))
                                ->relationship('organization', 'name', fn (Builder $query) => $query->when(userHasTenant(), function ($query) {
                                    return $query->where('tenant_id', userTenantId());
                                }))
                                ->disabled(function (Get $get) {
                                    return $get('project.is_synchronized_with_parent');
                                })
                                ->preload(),

                            Select::make('User_sponsor_id')
                                ->label('Particulier')
                                ->searchable()
                                ->visible(fn (Get $get) => $get('project.sponsor_type') == User::class)
                                ->required(fn (Get $get) => $get('project.sponsor_type') == User::class and !$get('project.is_synchronized_with_parent'))
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name} - {$record->email}")
                                ->relationship('user', 'first_name', function (Builder $query) {
                                    return $query->when(userHasTenant(), function ($query) {
                                        return $query->where('tenant_id', userTenantId());
                                    })->orderBy('first_name');
                                })
                                ->disabled(function (Get $get) {
                                    return $get('project.is_synchronized_with_parent');
                                })
                                ->preload(),
                        ]),
                ]),
        ];
    }

    public function submit()
    {
        $state = $this->form->getState();

        if ($state['project']['is_synchronized_with_parent'] ?? false) {
            $parentProject = $this->project->parentProject;

            $this->project->update([
                'is_synchronized_with_parent' => true,
                'sponsor_type' => $parentProject->sponsor_type,
                'sponsor_id' => $parentProject->sponsor_id,
            ]);

            defaultSuccessNotification(
                title: "Les données ont été mises à jour avec les informations du projet parent.",
                description: "Si les informations du projet parents sont modifiées, elles le seront également sur ce sous projet"
            );
            return;
        }

        $this->project->auditors()->sync($this->data['auditors']);

        $this->project->update([
            'referent_id' => $state['project']['referent_id'],
            'sponsor_type' => $state['project']['sponsor_type'],
            'sponsor_id' => match ($state['project']['sponsor_type']) {
                User::class => $state['User_sponsor_id'],
                Organization::class => $state['Organization_sponsor_id']
            },
        ]);

        $childrenProjectsSynchronized = $this->project->childrenProjects()->where('is_synchronized_with_parent', true)->get();

        foreach ($childrenProjectsSynchronized as $childProject) {
            $childProject->auditors()->sync($this->data['auditors']);
        }

        if ($childrenProjectsSynchronized->count() > 0) {
            $childrenProjectsSynchronized->toQuery()->update([
                'referent_id' => $state['project']['referent_id'],
                'sponsor_type' => $state['project']['sponsor_type'],
                'sponsor_id' => match ($state['project']['sponsor_type']) {
                    User::class => $state['User_sponsor_id'],
                    Organization::class => $state['Organization_sponsor_id']
                },
            ]);
        }


        Notification::make()
            ->title('Projet mis à jour.')
            ->body('Les différents rôles sur le projet ont été mis à jour.')
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->project,
            title: 'Mise à jour des objectifs du projet',
            url: route('projects.show.goals', ['project' => $this->project])
        );

    }

    protected function getFormModel(): Project
    {
        return $this->project;
    }

    public function render()
    {
        return view('livewire.forms.projects.project-relations-form');
    }
}
