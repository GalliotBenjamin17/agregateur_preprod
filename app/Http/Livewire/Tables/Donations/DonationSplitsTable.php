<?php

namespace App\Http\Livewire\Tables\Donations;

use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class DonationSplitsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Donation $donation = null;

    public ?Project $project = null;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return DonationSplit::with([
            'project.parentProject',
            'projectCarbonPrice',
            'childrenSplits',
        ])->when($this->donation, function ($query) {
            return $query->where('donation_id', $this->donation->id);
        });
    }

    protected function getTableRecordUrlUsing(): \Closure
    {
        return fn (Model $record): string => route('projects.show.donations', ['project' => $record->project]);
    }

    protected function getTableFilters(): array
    {
        return [
            TernaryFilter::make('type')
                ->trueLabel('Fléchage projets et sous-projets')
                ->falseLabel('Seulement sur les sous-projets')
                ->placeholder('Seulement sur les projets')
                ->queries(
                    true: fn (Builder $query) => $query,
                    false: fn (Builder $query) => $query->whereNotNull('donation_split_id'),
                    blank: fn (Builder $query) => $query->whereNull('donation_split_id'),
                ),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('project.name')
                ->description(function (DonationSplit $record) {

                    if ($record->childrenSplits->count()) {
                        return "Fléchages: " . $record->childrenSplits->pluck('project')->unique()->join('name');
                    }

                    return 'Fléchage projet';
                })
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->whereRelation('project', 'name', 'LIKE', "%{$search}%");
                })
                ->label('Projet'),
            TextColumn::make('amount')
                ->label('Montant')
                ->formatStateUsing(fn ($state) => format($state))
                ->description(function (DonationSplit $record) {
                    return $record->tonne_co2.' tCO2, Prix tonne : '.TVAHelper::getTTC($record->projectCarbonPrice->price).' € TTC';
                })
                ->suffix(' € TTC')
                ->searchable(),
            TextColumn::make('splitBy.name')
                ->label('Temporalité')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('\A H:i \l\e d/m/Y');
                })
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->whereRelation('splitBy', 'first_name', 'LIKE', "%{$search}%")
                        ->orWhereRelation('splitBy', 'last_name', 'LIKE', "%{$search}%");
                })
                ->sortable(),
        ];
    }

    public function render()
    {
        return view('livewire.tables.donations.donation-splits-table');
    }
}
