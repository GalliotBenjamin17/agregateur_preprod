<?php

namespace App\Http\Livewire\Tables\Projects;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Models\News;
use App\Models\PartnerProject;
use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PartnersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Project $project;

    protected $listeners = [
        'partnerProjectAdded' => 'render',
    ];

    protected function getTableQuery(): Builder
    {
        return PartnerProject::where('project_id', $this->project->id)
            ->with([
                'partner',
            ]);
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('details')
                ->label('Details')
                ->url(function (PartnerProject $partnerProject) {
                    return route('projects.show.partners.details', [
                        'project' => $partnerProject->project,
                        'partnerProject' => $partnerProject,
                    ]);
                }),

            DeleteAction::make()
                ->modalHeading(function (News $news) {
                    return 'Êtes-vous sûr de vouloir supprimer ce partenaire ?';
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('partner.name')
                ->label('Nom du partenaire'),

            TextColumn::make('commission_type')
                ->formatStateUsing(fn (PartnerProject $partnerProject) => $partnerProject->commission_type->displayName())
                ->label('Type de commission')
                ->sortable(),

            TextColumn::make('id')
                ->formatStateUsing(function (PartnerProject $partnerProject) {
                    return match ($partnerProject->commission_type) {
                        CommissionTypeEnum::Numerical => format($partnerProject->commission_numerical).' € HT',
                        CommissionTypeEnum::Percentage => $partnerProject->commission_percentage.' %',
                    };
                })
                ->description(function (PartnerProject $partnerProject) {
                    if (! $partnerProject->project->amount_wanted_ttc) {
                        return null;
                    }

                    if ($partnerProject->commission_type == CommissionTypeEnum::Percentage) {
                        return 'Equivalent : '.format($partnerProject->project->amount_wanted_ttc * ($partnerProject->commission_percentage / 100)).' € TTC';
                    }

                    if ($partnerProject->commission_type == CommissionTypeEnum::Numerical) {
                        return 'Equivalent : '.format(($partnerProject->commission_numerical / $partnerProject->project->amount_wanted_ttc) * 100).' %';
                    }

                })
                ->label('Commission'),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'updated_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function render()
    {
        return view('livewire.tables.projects.partners-table');
    }
}
