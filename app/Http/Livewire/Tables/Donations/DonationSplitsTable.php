<?php

namespace App\Http\Livewire\Tables\Donations;

use App\Helpers\DonationHelper;
use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Project;
use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
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
                        return 'Fléchages: ' . $record->childrenSplits->pluck('project')->unique()->join('name');
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
                ->getStateUsing(function (DonationSplit $record) {
                    // For parent rows, show allocated amount (sum of children); for leaves, show own amount
                    if ($record->childrenSplits->count() > 0) {
                        return $record->childrenSplits->sum('amount');
                    }
                    return $record->amount;
                })
                ->formatStateUsing(fn ($state) => format($state))
                ->description(function (DonationSplit $record) {
                    $base = $record->tonne_co2.' tCO2, Prix tonne : '.TVAHelper::getTTC($record->projectCarbonPrice->price).' € TTC';
                    if ($record->childrenSplits->count() > 0) {
                        $remaining = max(0, $record->amount - $record->childrenSplits->sum('amount'));
                        if ($remaining > 0) {
                            $base .= ' • Reste à flécher: '.format($remaining).' € TTC';
                        }
                    }
                    return $base;
                })
                ->suffix(' € TTC')
                ->searchable(),

            TextColumn::make('splitBy.name')
                ->label('Temporalité')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('À H:i \\l\\e d/m/Y');
                })
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->whereRelation('splitBy', 'first_name', 'LIKE', "%{$search}%")
                        ->orWhereRelation('splitBy', 'last_name', 'LIKE', "%{$search}%");
                })
                ->sortable(),
        ];
    }

    

    protected function getTableActions(): array
    {
        return [
            Action::make('Flécher')
                ->visible(function (DonationSplit $record) {
                    // Allow split-of-split only on parent rows with remaining amount
                    return is_null($record->donation_split_id)
                        && ($record->childrenSplits->sum('amount') < $record->amount);
                })
                ->action(function (DonationSplit $record, array $data): void {
                    DonationHelper::buildSplitOfSplit(donationSplit: $record, project: $record->project, split: $data);
                    Notification::make()->title('Fléchage effectué.')->success()->send();
                })
                ->mountUsing(function (ComponentContainer $form, DonationSplit $record) {
                    $form->fill([
                        'amount' => max(0, $record->amount - $record->childrenSplits->sum('amount')),
                    ]);
                })
                ->slideOver()
                ->form([
                    Select::make('project_id')
                        ->label('Sous-projet')
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->helperText(function (callable $get) {
                            $projectId = $get('project_id');
                            if (! $projectId) {
                                return 'Sélectionnez un sous-projet';
                            }

                            $child = Project::find($projectId);
                            if (! $child) {
                                return null;
                            }

                            // Reste à financer cohérent avec la page du sous-projet
                            $costTtc = (float) ($child->cost_global_ttc ?? 0);
                            $current = \App\Models\DonationSplit::where('project_id', $child->id)
                                ->whereDoesntHave('childrenSplits')
                                ->sum('amount');
                            $remaining = max(0, $costTtc - $current);

                            return 'Reste à financer sur ce sous-projet: ' . format($remaining) . ' € TTC';
                        })
                        ->options(function (DonationSplit $record) {
                            return $record->project->childrenProjects()
                                ->get()
                                ->pluck('name', 'id')
                                ->toArray();
                        }),
                    TextInput::make('amount')
                        ->label('Montant TTC')
                        ->suffix(' € TTC')
                        ->minValue(1)
                        ->required()
                        ->helperText(function (DonationSplit $record) {
                            $remaining = max(0, $record->amount - $record->childrenSplits->sum('amount'));
                            return 'Reste à flécher sur cette contribution: '.format($remaining).' € TTC';
                        })
                        ->numeric()
                        ->step('.01')
                        ->rules([
                            function (DonationSplit $record) {
                                return function (string $attribute, $value, Closure $fail) use ($record) {
                                    $remaining = max(0, $record->amount - $record->childrenSplits->sum('amount'));
                                    if ($value > $remaining) {
                                        $fail('Le montant dépasse le reste à flécher: '.format($remaining).' € TTC');
                                    }
                                };
                            },
                            function (callable $get) {
                                return function (string $attribute, $value, Closure $fail) use ($get) {
                                    $projectId = $get('project_id');
                                    if (! $projectId) {
                                        return;
                                    }
                                    $child = Project::find($projectId);
                                    if (! $child) {
                                        return;
                                    }
                                    $costTtc = (float) ($child->cost_global_ttc ?? 0);
                                    $current = \App\Models\DonationSplit::where('project_id', $child->id)
                                        ->whereDoesntHave('childrenSplits')
                                        ->sum('amount');
                                    $remaining = max(0, $costTtc - $current);
                                    if ($value > $remaining) {
                                        $fail('Le montant dépasse le reste à financer du sous-projet: ' . format($remaining) . ' € TTC');
                                    }
                                };
                            }
                        ]),
                ]),
        ];
    }

    public function render()
    {
        return view('livewire.tables.donations.donation-splits-table');
    }
}
