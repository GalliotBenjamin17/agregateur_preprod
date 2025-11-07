<?php

namespace App\Http\Livewire\Tables\Donations;

use App\Exceptions\DonationSplitAmountIsNullException;
use App\Exceptions\MoreDonationSplitAmountThanExpectedException;
use App\Helpers\DonationHelper;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class DonationSplitsProjectTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Project $project;

    protected function getTableQuery(): Builder
    {
        return DonationSplit::with([
            'donation.related',
            'childrenSplits',
            'project',
        ])->withCount([
            'childrenSplits',
        ])
            ->whereIn('project_id', $this->project->descendantIds(includeSelf: true))
            ->whereDoesntHave('childrenSplits') // only leaf splits to avoid duplicates
            ->orderBy('project_id');
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return function (DonationSplit $record): string {
            if (is_null($record->donation)) {
                return false;
            }

            return route('donations.show.split', ['donation' => $record->donation]);
        };
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Flécher')
                ->visible(function (DonationSplit $record) {
                    // Only allow splitting from the current project's own rows
                    return $this->project->hasChildrenProjects()
                        && ($record->project_id === $this->project->id)
                        && ($record->childrenSplits->sum('amount') < $record->amount);
                })
                ->action(function (DonationSplit $record, array $data): void {
                    try {
                        DonationHelper::buildSplitOfSplit(donationSplit: $record, project: $this->project, split: $data);

                        Notification::make()
                            ->title('Fléchage de la contribution effectué avec succès.')
                            ->success()
                            ->send();
                    } catch (DonationSplitAmountIsNullException $exception) {
                        Notification::make('DonationSplitAmountIsNullException')
                            ->title('Erreur dans le fléchage de la contribution')
                            ->body('Ce projet ne peut plus recevoir de contribution car le montant recherché est complété à 100%.')
                            ->danger()
                            ->send();
                    } catch (MoreDonationSplitAmountThanExpectedException) {
                        return;
                    }
                })
                ->mountUsing(function (ComponentContainer $form, Model $record) {
                    $form->fill([
                        'amount' => $record->amount,
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

                            // Reste à financer cohérent avec la page du sous-projet:
                            // cost_global_ttc - somme des splits FEUILLES pour ce sous-projet (pas les parents)
                            $costTtc = (float) ($child->cost_global_ttc ?? 0);
                            $current = \App\Models\DonationSplit::where('project_id', $child->id)
                                ->whereDoesntHave('childrenSplits')
                                ->sum('amount');
                            $remaining = max(0, $costTtc - $current);

                            return 'Reste à financer sur ce sous-projet: ' . format($remaining) . ' € TTC';
                        })
                        ->options(function () {
                            return $this->project->childrenProjects()
                                ->get()
                                ->filter(function ($item) {
                                    if (is_null($item->amount_wanted_ttc)) {
                                        return false;
                                    }

                                    return $item->donation_splits_sum_amount < $item->amount_wanted_ttc;
                                })
                                ->pluck('name', 'id')
                                ->toArray();
                        }),
                    TextInput::make('amount')
                        ->label('Montant TTC')
                        ->suffix(' € TTC')
                        ->minValue(1)
                        ->required()
                        ->helperText(function (Model $record) {
                            return 'Vous pouvez encore flécher ' . (format($record->amount - $record->childrenSplits->sum('amount'))) . ' € TTC (sur la contribution sélectionnée)';
                        })
                        ->numeric()
                        ->step('.01')
                        ->rules([
                            function (DonationSplit $record) {
                                return function (string $attribute, $value, Closure $fail) use ($record) {
                                    if ($record->childrenSplits->sum('amount') + $value > $record->amount) {
                                        $fail('Le montant maximal du fléchage est de '.(format($record->amount - $record->childrenSplits->sum('amount'))).' € TTC');
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
                ])
                ->modalHeading('Fléchage de la contribution')
                ->modalSubmitActionLabel('Flécher'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('donation.related.name')
                ->description(function (Model $record) {
                    return Str::limit("ID ctrb.: {$record->donation?->id}", 15);
                })
                ->label('Donateur'),

            TextColumn::make('amount')
                ->label('Montant affilié au projet')
                ->formatStateUsing(fn ($state) => format($state))
                ->description(fn (DonationSplit $record) => $record->tonne_co2.' tCO2')
                ->suffix(' € TTC'),

            TextColumn::make('children_splits_count')
                ->label('Fléchage sous-projet')
                ->formatStateUsing(function (DonationSplit $record) {
                    if ($record->project_id !== $this->project->id) {
                        return $record->project?->name ?? 'Sous-projet inconnu';
                    }
                    return 'Aucun fléchage sous-projet';
                })
                ->description(function (DonationSplit $record) {
                    if ($record->project_id !== $this->project->id) {
                        return format($record->amount).' € TTC';
                    }
                    return 'Aucune répartition';
                })
                ->hidden($this->project->hasParent()),

            TextColumn::make('splitBy.name')
                ->label('Temporalité')
                ->description(function (Model $record): ?string {
                    // Escape 'l' (day name) and 'e' (timezone) to render literal "le"
                    return $record->created_at->format('À H:i \l\e d/m/Y');
                })
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->whereRelation('splitBy', 'first_name', 'LIKE', "%{$search}%")
                        ->orWhereRelation('splitBy', 'last_name', 'LIKE', "%{$search}%");
                })
                ->sortable(['created_at']),
        ];
    }

    public function render()
    {
        return view('livewire.tables.donations.donation-splits-project-table');
    }
}
