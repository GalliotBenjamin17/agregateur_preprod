<?php

namespace App\Http\Livewire\Tables\Donations;

use App\Enums\Roles;
use App\Exceptions\DonationSplitAmountIsNullException;
use App\Helpers\DonationHelper;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class IndexTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Organization $organization = null;

    public ?User $user = null;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return Donation::with([
            'createdBy',
            'donationSplits',
        ])->withCount([
            'donationSplits',
        ])
            ->when($this->organization, function ($query) {
                return $query->where('related_id', $this->organization->id)->where('related_type', get_class($this->organization));
            })
            ->when($this->user, function ($query) {
                return $query->where('related_id', $this->user->id)->where('related_type', get_class($this->user));
            });
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('update_batch_email')
                ->label('Flécher')
                ->action(function (Collection $records, array $data) {
                    $splits = [];
                    foreach ($records as $record) {
                        $splits[] = [
                            'type' => 'project',
                            'data' => [
                                'project_id' => $data['project_id'],
                                'amount' => $record->getAvailableAmount(),
                            ],
                        ];
                    }

                    try {
                        DonationHelper::buildSplit(donation: $record, splits: $splits);
                    } catch (DonationSplitAmountIsNullException $exception) {
                        Notification::make('DonationSplitAmountIsNullException')
                            ->title('Erreur dans le fléchage de la contribution')
                            ->body('Ce projet ne peut plus recevoir de contribution car le montant recherché est complété à 100%.')
                            ->danger()
                            ->send();
                    } catch (\Exception $exception) {
                        Notification::make('Exception')
                            ->title('Erreur dans le fléchage de la contribution')
                            ->danger()
                            ->send();
                    }
                })
                ->visible(function (Donation $record) {
                    return $record->donationSplits()->whereNull('donation_split_id')->sum('amount') == 0;
                })
                ->form([
                    Placeholder::make('infos')
                        ->hiddenLabel()
                        ->content(
                            new HtmlString("<span class='font-semibold'>L'intégralité des contributions sélectionnées seront fléché sur ce projet.</span>")
                        ),
                    Select::make('project_id')
                        ->label('Projet')
                        ->required()
                        ->searchable()
                        ->options(
                            Project::select(['id', 'name', 'parent_project_id', 'amount_wanted_ttc'])
                                ->whereNull('parent_project_id')
                                ->orHas('childrenProjects')
                                ->withSum('donationSplits', 'amount')
                                ->withCount('childrenProjects')
                                ->get()
                                ->filter(function ($item) {
                                    if ($item->children_projects_count > 0) {
                                        return true;
                                    }

                                    if (is_null($item->amount_wanted_ttc)) {
                                        return false;
                                    }

                                    return $item->donation_splits_sum_amount < $item->amount_wanted_ttc;
                                })
                                ->pluck('name', 'id')
                                ->toArray()
                        ),
                ])
                ->slideOver()
                ->modalSubmitActionLabel('Flécher'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('is_done')
                ->toggle()
                ->label("Uniquement les contributions où le fléchage n'est pas complet")
                ->query(fn (Builder $query): Builder => $query->where('is_donation_splits_full', false)),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('generate_pdf')
                    ->label(function (Donation $record) {
                        if ($record->certificate_pdf_path) {
                            return 'Régénérer le certificat';
                        }

                        return 'Générer le certificat';
                    })
                    ->action(function (Donation $record) {
                        $path = DonationHelper::generateCertificate($record);
                        $record->refresh();

                        Notification::make()
                            ->title('Certificat généré')
                            ->body('Le certificat a été généré avec les dernière attributions aux projets.')
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('Ouvrir')
                                    ->url(asset($record->certificate_pdf_path), shouldOpenInNewTab: true)
                                    ->button(),
                            ])
                            ->persistent()
                            ->success()
                            ->send();

                    }),
                Action::make('display_pdf')
                    ->label('Afficher le certificat')
                    ->visible(function (Donation $record) {
                        return $record->certificate_pdf_path;
                    })
                    ->url(url: function (Donation $record) {
                        return asset($record->certificate_pdf_path);
                    }, shouldOpenInNewTab: true),

                Action::make('redirect')
                    ->label('Rediriger')
                    ->color('warning')
                    ->icon('heroicon-m-arrow-right-on-rectangle')
                    ->modalIcon('heroicon-m-exclamation-triangle')
                    ->modalSubmitActionLabel('Réafilier cette donation vers une organisation')
                    ->modalDescription("Cette action n'est possible que dans le sens `Utilisateur` vers `Organisation`. Cette action est dont irréversible.")
                    ->visible(function (Donation $donation) {
                        return $donation->related instanceof User and request()->user()->hasRole(Roles::Admin);
                    })
                    ->form(function (Donation $donation) {
                        $tenant = $donation->tenant;
                        $organizations = $tenant->organizations()->get();

                        return [
                            Select::make('organization_id')
                                ->label('Organisation sur laquelle la contribution doit être redirigée :')
                                ->required()
                                ->searchable()
                                ->options($organizations->pluck('name', 'id')),

                            Toggle::make('confirmation')
                                ->required()
                                ->accepted()
                                ->onColor('warning')
                                ->helperText("Nous stockons dans la base de données l'heure et l'utilisateur ayant effectué la redirection.")
                                ->label('Je confirme que cette action peut entrainer des erreurs comptables dont je suis le seul responsable.'),
                        ];
                    })
                    ->action(function (Donation $donation, array $data) {

                        $donation->update([
                            'related_type' => Organization::class,
                            'related_id' => $data['organization_id'],
                            'redirected_at' => now(),
                            'redirected_by' => request()->user()->id,
                        ]);
                        $donation->generateCertificate();

                        Notification::make()
                            ->title('Nouveau certificat généré')
                            ->body('Le certificat a été regénéré avec la nouvelle attribution.')
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('Ouvrir')
                                    ->url(asset($donation->certificate_pdf_path), shouldOpenInNewTab: true)
                                    ->button(),
                            ])
                            ->persistent()
                            ->success()
                            ->send();
                    })
                    ->slideOver(),
            ]),

        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return fn (Donation $record): string => route('donations.show.split', ['donation' => $record->id]);
    }

    public function isTableRecordSelectable(): ?Closure
    {
        return fn (Donation $record): bool => ! $record->isSplitsFull() and request()->user()->hasAnyRole([Roles::Admin, Roles::LocalAdmin]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('related.name')
                ->default('Inconnu')
                ->label('Effectué par')
                ->description(function (Model $record): string {
                    return match ($record->related ? get_class($record->related) : null) {
                        User::class => 'Acteur',
                        Organization::class => 'Organisation',
                        default => 'Type de contributeur inconnu'
                    };
                })
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->whereHasMorph('related', [Organization::class], function ($q) use ($search) {
                            return $q->where('name', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHasMorph('related', [User::class], function ($q) use ($search) {
                            return $q->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%");
                        });
                }),

            TextColumn::make('source')
                ->label('Source')
                ->formatStateUsing(fn ($state) => \Arr::get($state, config('values.donations.map')))
                ->description(function (Donation $record): ?string {
                    return $record->external_id;
                }),

            TextColumn::make('amount')
                ->formatStateUsing(fn ($state) => format($state, 2))
                ->label('Montant')
                ->sortable()
                ->searchable(['amount'])
                ->suffix('€ TTC'),
            TextColumn::make('id')
                ->formatStateUsing(function (Donation $record) {
                    return format($record->donationSplits->whereNull('donation_split_id')->sum('amount'));
                })
                ->label('Montant fléché')
                ->suffix('€ TTC'),
            TextColumn::make('createdBy.name')
                ->label('Création')
                ->default('Inconnu')
                ->description(function (Model $record): ?string {
                    return $record->created_at?->format('\A H:i \l\e d/m/Y');
                }),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function render()
    {
        return view('livewire.tables.donations.index-table');
    }
}
