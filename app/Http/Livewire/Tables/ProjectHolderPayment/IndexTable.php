<?php

namespace App\Http\Livewire\Tables\ProjectHolderPayment;

use App\Enums\Roles;
use App\Helpers\TVAHelper;
use App\Models\Project;
use App\Models\ProjectHolderPayment;
use App\Services\Models\ProjectHolderPaymentService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class IndexTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public Project $project;

    protected $listeners = [
        'projectHolderPaymentAdded' => 'render',
    ];

    protected function getTableQuery(): Builder
    {
        return ProjectHolderPayment::where('project_id', $this->project->id);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('update')
                ->slideOver()
                ->label('Mettre à jour')
                ->color('info')
                ->icon(null)
                ->mountUsing(function (ComponentContainer $form, ProjectHolderPayment $projectHolderPayment) {
                    $form->fill([
                        'projectHolderPayment' => $projectHolderPayment->toArray(),
                        'receipt' => str_replace('/storage/', '', $projectHolderPayment->receipt),
                    ]);
                })
                ->visible(request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
                ->action(function (ProjectHolderPayment $projectHolderPayment, array $data) {
                    $projectHolderPaymentService = new ProjectHolderPaymentService(projectHolderPayment: $projectHolderPayment);
                    $projectHolderPaymentService->update([
                        ...$data['projectHolderPayment'],
                        'receipt' => \Arr::get($data, 'receipt') ? '/storage/'.$data['receipt'] : null,
                    ]);
                })
                ->form([
                    Grid::make()
                        ->schema([

                            TextInput::make('projectHolderPayment.amount_ht')
                                ->label('Montant (€ HT)')
                                ->suffix(' € HT')
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $set('projectHolderPayment.amount', TVAHelper::getTTC($state));
                                })
                                ->required(),

                            TextInput::make('projectHolderPayment.amount')
                                ->label('Montant (€ TTC)')
                                ->suffix(' € TTC')
                                ->required(),

                            DatePicker::make('projectHolderPayment.created_at')
                                ->label('Paiement effectué le')
                                ->required(),

                            FileUpload::make('receipt')
                                ->openable()
                                ->columnSpanFull()
                                ->downloadable()
                                ->getUploadedFileNameForStorageUsing(function ($file): string {
                                    return formatFileName($file->getClientOriginalName());
                                })
                                ->label('Justificatif'),
                        ]),
                ]),

            Action::make('delete')
                ->label('Supprimer')
                ->color('danger')
                ->visible(request()->user()->hasRole([Roles::Admin, Roles::LocalAdmin]))
                ->icon(null)
                ->action(function (ProjectHolderPayment $projectHolderPayment) {
                    $projectHolderPayment->delete();
                })
                ->requiresConfirmation(),

        ];
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('amount')
                ->suffix('€ TTC')
                ->sortable()
                ->description(function (ProjectHolderPayment $projectHolderPayment) {
                    return $projectHolderPayment->amount_ht.' € HT';
                })
                ->label('Montant TTC'),

            TextColumn::make('createdBy.name')
                ->label('Enregistré par ')
                ->default('Inconnu')
                ->description(function (ProjectHolderPayment $projectHolderPayment): ?string {
                    return $projectHolderPayment->created_at->format('d/m/Y');
                })
                ->sortable(['created_at']),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Aucun paiement référencé';
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
        return view('livewire.tables.project-holder-payment.index-table');
    }
}
