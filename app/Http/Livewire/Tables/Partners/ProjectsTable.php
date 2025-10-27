<?php

namespace App\Http\Livewire\Tables\Partners;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Models\Partner;
use App\Models\PartnerProject;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ProjectsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Partner $partner;

    protected function getTableQuery(): Builder
    {
        return PartnerProject::where('partner_id', $this->partner->id)
            ->with([
                'project',
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
                    if ($partnerProject->project) {
                        return route('projects.show.partners.details', [
                            'project' => $partnerProject->project,
                            'partnerProject' => $partnerProject,
                        ]);
                    }

                    return '#';
                }),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('project.name')
                ->limit(50)
                ->label('Projet'),

            TextColumn::make('commission_type')
                ->formatStateUsing(fn (PartnerProject $partnerProject) => $partnerProject->commission_type->displayName())
                ->label('Type de commission')
                ->sortable(),

            TextColumn::make('id')
                ->formatStateUsing(function (PartnerProject $partnerProject) {
                    return match ($partnerProject->commission_type) {
                        CommissionTypeEnum::Numerical => format($partnerProject->commission_numerical).' €',
                        CommissionTypeEnum::Percentage => $partnerProject->commission_percentage.' %',
                    };
                })
                ->description(function (PartnerProject $partnerProject) {
                    if (! $partnerProject->project or ! $partnerProject->project?->amount_wanted_ttc) {
                        return null;
                    }

                    if ($partnerProject->commission_type == CommissionTypeEnum::Percentage) {
                        return 'Equivalent : '.format($partnerProject->project->amount_wanted_ttc * ($partnerProject->commission_percentage / 100)).' €';
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
        return view('livewire.tables.partners.projects-table');
    }
}
