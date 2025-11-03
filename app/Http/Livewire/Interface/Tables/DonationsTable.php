<?php

namespace App\Http\Livewire\Interface\Tables;

use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Filament\Tables\Table;


class DonationsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable {
        table as baseTable;
    }

    public ?Organization $organization = null;

    public ?User $user = null;

    public function table(Table $table): Table
    {
        return $this->baseTable($table)
            ->filtersTriggerAction(function (Action $action): Action {
                return $action
                    ->label('Filtres') 
                    ->button()          
                    ->icon('heroicon-m-funnel');
            });
    }

    protected function getTableQuery(): Builder
    {
        return Donation::with([
            'createdBy',
            'donationSplits',
        ])->withCount([
            'donationSplits',
        ])
            ->when($this->organization, function ($query) {
                return $query->where('related_id', $this->organization->id)
                    ->where('related_type', get_class($this->organization));
            })
            ->when($this->user, function ($query) {
                return $query->where('related_id', $this->user->id)->where('related_type', get_class($this->user));
            });
    }

    protected function getTableActions(): array
    {
        return [

            Action::make('display_pdf')
                ->label('Certificat pdf')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->tooltip('Afficher le certificat')
                ->iconPosition(IconPosition::After)
                ->visible(function (Donation $record) {
                    return $record->certificate_pdf_path;
                })
                ->url(url: function (Donation $record) {
                    return asset($record->certificate_pdf_path);
                }, shouldOpenInNewTab: true),

        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('project_filter')
                ->label('Projet')
                ->multiple()
                ->searchable()
                ->preload()
                ->options($this->getProjectFilterOptions())
                ->query(function (Builder $query, array $data): Builder {
                    $projectIds = array_filter($data['values'] ?? [], fn ($value) => filled($value));

                    if (! count($projectIds)) {
                        return $query;
                    }

                    return $query->whereHas('donationSplits.project', function (Builder $projectQuery) use ($projectIds) {
                        $projectQuery->whereIn('projects.id', $projectIds);
                    });
                }),

            SelectFilter::make('year_filter')
                ->label('Année')
                ->multiple()
                ->preload()
                ->options($this->getYearFilterOptions())
                ->query(function (Builder $query, array $data): Builder {
                    $years = array_filter($data['values'] ?? [], fn ($value) => filled($value));

                    if (! count($years)) {
                        return $query;
                    }

                    return $query->where(function (Builder $yearQuery) use ($years) {
                        foreach ($years as $year) {
                            $yearQuery->orWhereYear('created_at', $year);
                        }
                    });
                }),
        ];
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('created_at')
                ->date('d/m/Y')
                ->weight(FontWeight::SemiBold)
                ->description(function (Donation $donation) {
                    return new HtmlString(
                        $this->filterDisplayableSplits($donation->donationSplits)
                            ->pluck('project')
                            ->pluck('name')
                            ->join('<br>')
                    );
                })
                ->label('Date et projets financés'),

            TextColumn::make('amount')
                ->formatStateUsing(function (Donation $donation) {
                    $totals = $this->calculateDonationTotals($donation);

                    return format(
                        $this->isOrganizationContext()
                            ? $totals['ht']
                            : $totals['ttc']
                    );
                })
                ->description(function (Donation $donation) {
                    $donationSplits = $this->filterDisplayableSplits($donation->donationSplits);
                    $suffix = $this->getDisplayAmountLabel();

                    $string = $donationSplits
                        ->map(function (DonationSplit $split) use ($suffix) {
                            $amount = $this->isOrganizationContext()
                                ? $this->getHtAmount($split)
                                : $split->amount;

                            return format($amount).' '.$suffix;
                        })
                        ->implode('<br>');

                    return new HtmlString($string);
                })
                ->label('Montant')
                ->prefix('Total: ')
                ->suffix(fn () => $this->getDisplayAmountLabel())
                ->summarize([
                    Summarizer::make()
                        ->label('Montant global')
                        ->using(fn (QueryBuilder $query): array => $this->getSummaryTotals($query))
                        ->formatStateUsing(function (array $state): string {
                            if ($this->isOrganizationContext()) {
                                return sprintf('%s € HT', format($state['ht']));
                            }

                            return sprintf('%s € TTC', format($state['ttc']));
                        }),
                ]),

            TextColumn::make('id')
                ->formatStateUsing(function (Donation $donation) {
                    $total = (float) $donation->donationSplits->whereNull('donation_split_id')->sum('tonne_co2');

                    return $this->formatTonneCo2($total);
                })
                ->description(function (Donation $donation) {

                    $tons = $this->filterDisplayableSplits($donation->donationSplits)
                        ->pluck('tonne_co2');

                    $string = '';

                    foreach ($tons as $ton) {
                        $string .= $this->formatTonneCo2((float) $ton).'T <br>';
                    }

                    return new HtmlString($string);
                })
                ->label('Équivalent Co2')
                ->prefix('Total: ')
                ->suffix('T')
                ->summarize([
                    Summarizer::make()
                        ->label('Total Tonne CO2')
                        ->using(fn (QueryBuilder $query): float => $this->getSummaryCo2Total($query))
                        ->formatStateUsing(fn (float $state): string => sprintf('%s T', $this->formatTonneCo2($state))),
                ]),
        ];
    }

    private function filterDisplayableSplits(Collection $splits): Collection
    {
        $projectIds = $this->getActiveFilterValues('project_filter');

        $splits->loadMissing([
            'project.parentProject',
        ]);

        return $splits->filter(function (DonationSplit $donationSplit) {
            return $this->shouldIncludeSplit($donationSplit);
        })->filter(function (DonationSplit $donationSplit) use ($projectIds) {
            if ($donationSplit->project) {
                $project = $donationSplit->project;
            } else {
                $project = $donationSplit->project()->first();
            }

            if ($projectIds) {
                if (in_array($project?->id, $projectIds, true)) {
                    return true;
                }

                return in_array($project?->parent_project_id, $projectIds, true);
            }

            return true;
        });
    }

    private function getSummaryTotals(QueryBuilder $query): array
    {
        $donationIds = (clone $query)->pluck('id');

        if ($donationIds->isEmpty()) {
            return [
                'ht' => 0,
                'ttc' => 0,
            ];
        }

        $splits = DonationSplit::query()
            ->whereIn('donation_id', $donationIds)
            ->with(['project'])
            ->withCount('childrenSplits')
            ->get();

        $splits = $this->filterDisplayableSplits($splits);

        $totalTtc = $splits->sum('amount');

        $totalHt = $splits->sum(fn (DonationSplit $split) => $this->getHtAmount($split));

        return [
            'ht' => $totalHt,
            'ttc' => $totalTtc,
        ];
    }

    private function getSummaryCo2Total(QueryBuilder $query): float
    {
        $donationIds = (clone $query)->pluck('id');

        if ($donationIds->isEmpty()) {
            return 0.0;
        }

        $splits = DonationSplit::query()
            ->whereIn('donation_id', $donationIds)
            ->with(['project'])
            ->withCount('childrenSplits')
            ->get();

        $splits = $this->filterDisplayableSplits($splits);

        return (float) $splits->sum('tonne_co2');
    }

    private function calculateDonationTotals(Donation $donation): array
    {
        $donationSplits = $this->filterDisplayableSplits($donation->donationSplits);

        return [
            'ht' => $donationSplits->sum(fn (DonationSplit $split) => $this->getHtAmount($split)),
            'ttc' => $donationSplits->sum('amount'),
        ];
    }

    private function getHtAmount(DonationSplit $split): float
    {
        if ($split->project && $split->project->subject_to_vat) {
            return TVAHelper::getHT($split->amount);
        }

        return $split->amount;
    }

    private function getDisplayAmountLabel(): string
    {
        return $this->isOrganizationContext() ? '€ HT' : '€ TTC';
    }

    private function formatTonneCo2(float $value): string
    {
        $rounded = round($value, 1);
        $roundedInt = round($rounded);

        if (abs($rounded - $roundedInt) < 0.00001) {
            return format($roundedInt, 0);
        }

        return format($rounded, 1);
    }

    private function isOrganizationContext(): bool
    {
        return $this->organization instanceof Organization;
    }

    private function shouldIncludeSplit(DonationSplit $donationSplit): bool
    {
        if ($donationSplit->donation_split_id !== null) {
            return true;
        }

        if (isset($donationSplit->children_splits_count)) {
            $hasChildSplits = $donationSplit->children_splits_count > 0;
        } elseif ($donationSplit->relationLoaded('childrenSplits')) {
            $hasChildSplits = $donationSplit->childrenSplits->isNotEmpty();
        } else {
            $hasChildSplits = $donationSplit->childrenSplits()->exists();
        }

        return ! $hasChildSplits;
    }

    private function getActiveFilterValues(string $key): array
    {
        $state = $this->getTableFilterState($key);

        if (isset($state['values'])) {
            return array_values(array_filter($state['values'], fn ($value) => filled($value)));
        }

        if (isset($state['value']) && filled($state['value'])) {
            return [$state['value']];
        }

        return [];
    }

    private function getProjectFilterOptions(): array
    {
        $donationIds = (clone $this->getTableQuery())->pluck('donations.id');

        if ($donationIds->isEmpty()) {
            return [];
        }

        return Project::query()
            ->whereNull('parent_project_id')
            ->where(function (Builder $query) use ($donationIds) {
                $query
                    ->whereHas('donationSplits', function (Builder $splitQuery) use ($donationIds) {
                        $splitQuery->whereIn('donation_id', $donationIds);
                    })
                    ->orWhereHas('childrenProjects.donationSplits', function (Builder $splitQuery) use ($donationIds) {
                        $splitQuery->whereIn('donation_id', $donationIds);
                    });
            })
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Project $project) => [$project->id => $project->name])
            ->toArray();
    }

    private function getYearFilterOptions(): array
    {
        return Donation::query()
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year', 'year')
            ->toArray();
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public function render()
    {
        return view('livewire.interface.tables.donations-table');
    }
}