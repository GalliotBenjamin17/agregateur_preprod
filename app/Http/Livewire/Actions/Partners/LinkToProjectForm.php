<?php

namespace App\Http\Livewire\Actions\Partners;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Enums\Roles;
use App\Models\PartnerProject;
use App\Models\Project;
use App\Services\Models\PartnerProjectService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class LinkToProjectForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Project $project;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajouter un partenaire au projet')
            ->visible(request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
            ->size('sm')
            ->form([
                Grid::make()
                    ->schema([
                        Select::make('partner_id')
                            ->label('Partenaire')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->relationship('partner', 'name', fn (Builder $query) => $query->when(userHasTenant(), function ($query) {
                                return $query->where('tenant_id', userTenantId());
                            })->whereNotIn('id', $this->project->projectPartners()->where('project_id', $this->project->id)->get()->pluck('partner_id')->toArray())),

                        Select::make('commission_type')
                            ->label('Type de commission')
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->options(CommissionTypeEnum::toArray()),

                        TextInput::make('commission_percentage')
                            ->label('Pourcentage de la commission')
                            ->suffix('%')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(fn (Get $get) => CommissionTypeEnum::tryFrom($get('commission_type')) == CommissionTypeEnum::Percentage)
                            ->visible(fn (Get $get) => CommissionTypeEnum::tryFrom($get('commission_type')) == CommissionTypeEnum::Percentage),

                        TextInput::make('commission_numerical')
                            ->label('Montant de la commission (€ HT)')
                            ->suffix(' € HT')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn (Get $get) => CommissionTypeEnum::tryFrom($get('commission_type')) == CommissionTypeEnum::Numerical)
                            ->visible(fn (Get $get) => CommissionTypeEnum::tryFrom($get('commission_type')) == CommissionTypeEnum::Numerical),

                    ]),
            ])
            ->action(function (array $data) {
                $partnerProjectService = new PartnerProjectService();
                $partnerProjectService->store([
                    ...$data,
                    'project_id' => $this->project->id,
                ]);

                $this->dispatch('partnerProjectAdded');

                Notification::make()
                    ->title('Partenaire ajouté sur le projet')
                    ->body('Vous pouvez accéder aux détails dans la table sur cette même page')
                    ->success()
                    ->send();
            })
            ->model(PartnerProject::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.partners.link-to-project-form');
    }
}
