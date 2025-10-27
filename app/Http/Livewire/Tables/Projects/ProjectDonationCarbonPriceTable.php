<?php

namespace App\Http\Livewire\Tables\Projects;

use App\Models\Project;
use App\Models\ProjectCarbonPrice;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ProjectDonationCarbonPriceTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public Project $project;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return ProjectCarbonPrice::with([
            'createdBy',
        ])->withCount([
            'donationsSplit',
        ])->withSum('donationsSplit', 'amount')
            ->withSum('donationsSplit', 'tonne_co2')
            ->where('project_id', $this->project->id);
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('price')
                ->suffix(' € HT')
                ->description(function (ProjectCarbonPrice $record) {
                    if ($record->sync_with_tenant) {
                        return 'Synchronisé';
                    }

                    return 'Non Synchronisé';
                })
                ->label('Prix tonne (HT)'),

            TextColumn::make('start_at')
                ->label("Date début d'activation")
                ->dateTime('H:i d/m/Y')
                ->sortable(),

            TextColumn::make('end_at')
                ->label("Date fin d'activation")
                ->default('Actif')
                ->formatStateUsing(function (Model $record) {
                    if ($record->end_at instanceof Carbon) {
                        return $record->end_at->format('H:i d/m/Y');
                    }

                    return 'Actif';
                })
                ->weight(function (Model $record) {
                    if ($record->end_at instanceof Carbon) {
                        return 'normal';
                    }

                    return 'semibold';
                })
                ->color(function (Model $record) {
                    if ($record->end_at instanceof Carbon) {
                        return 'black';
                    }

                    return 'success';
                })
                ->icon(function (Model $record) {
                    return $record->end_at ? null : 'heroicon-o-check-circle';
                })
                ->sortable(),

            TextColumn::make('donations_split_sum_amount')
                ->label('Fléchages avec ce prix')
                ->formatStateUsing(fn ($state) => format($state))
                ->suffix(' € TTC')
                ->default(0)
                ->sum('donationsSplit', 'amount')
                ->description(function (Model $record) {
                    if ($record->donations_split_count <= 1) {
                        return $record->donations_split_count.' fléchage';
                    }

                    return $record->donations_split_count.' fléchages';
                }),

            TextColumn::make('donations_split_sum_tonne_co2')
                ->label('Tonnes Co2 (base HT)')
                ->formatStateUsing(fn ($state) => number_format($state, 2, ',', ' '))
                ->default(0)
                ->suffix(' tCo2')
                ->sum('donationsSplit', 'tonne_co2'),

            TextColumn::make('createdBy.name')
                ->label('Création')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('\A H:i \l\e d/m/Y');
                }),
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
        return view('livewire.tables.projects.project-donation-carbon-price-table');
    }
}
