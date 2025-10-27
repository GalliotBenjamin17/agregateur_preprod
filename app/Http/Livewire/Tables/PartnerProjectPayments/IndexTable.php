<?php

namespace App\Http\Livewire\Tables\PartnerProjectPayments;

use App\Enums\Models\PartnerProjectPayments\PaymentStateEnum;
use App\Models\News;
use App\Models\PartnerProject;
use App\Models\PartnerProjectPayment;
use App\Models\Project;
use App\Services\Models\PartnerProjectPaymentService;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
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

    public ?PartnerProject $partnerProject = null;

    public ?Project $project = null;

    protected $listeners = [
        'partnerProjectPaymentAdded' => 'render',
    ];

    protected function getTableQuery(): Builder
    {
        return PartnerProjectPayment::when($this->partnerProject, function ($query) {
            return $query->where('partner_project_id', $this->partnerProject->id);
        })->when($this->project, function ($query) {
            return $query->whereRelation('partnerProject', 'project_id', $this->project->id);
        });
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('update')
                ->modalHeading(function (News $news) {
                    return $news->name;
                })
                ->slideOver()
                ->label('Mettre à jour')
                ->color('info')
                ->icon(null)
                ->mountUsing(function (ComponentContainer $form, PartnerProjectPayment $partnerProjectPayment) {
                    $form->fill([
                        'partnerProjectPayment' => $partnerProjectPayment->toArray(),
                        'receipt' => str_replace('/storage/', '', $partnerProjectPayment->receipt),
                    ]);
                })
                ->action(function (PartnerProjectPayment $partnerProjectPayment, array $data) {
                    $partnerProjectPaymentService = new PartnerProjectPaymentService(partnerProjectPayment: $partnerProjectPayment);
                    $partnerProjectPaymentService->update([
                        ...$data['partnerProjectPayment'],
                        'receipt' => \Arr::get($data, 'receipt') ? '/storage/'.$data['receipt'] : null,
                    ]);
                })
                ->form([
                    Grid::make()
                        ->schema([
                            Select::make('partnerProjectPayment.payment_state')
                                ->searchable()
                                ->label('Statut')
                                ->reactive()
                                ->required()
                                ->options(PaymentStateEnum::toArray()),

                            DateTimePicker::make('partnerProjectPayment.scheduled_at')
                                ->label('Planifié à')
                                ->native()
                                ->minDate(now())
                                ->seconds(false)
                                ->required(fn (Get $get) => $get('partnerProjectPayment.payment_state') == PaymentStateEnum::Scheduled->databaseKey())
                                ->visible(fn (Get $get) => $get('partnerProjectPayment.payment_state') == PaymentStateEnum::Scheduled->databaseKey()),

                            TextInput::make('partnerProjectPayment.amount')
                                ->label('Montant')
                                ->suffix(' € TTC')
                                ->required(),

                            DatePicker::make('partnerProjectPayment.created_at')
                                ->required()
                                ->label('Effectué le'),

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
                ->icon(null)
                ->action(function (PartnerProjectPayment $partnerProjectPayment) {
                    $partnerProjectPayment->delete();
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('partnerProject.partner.name')
                ->visible(is_null($this->partnerProject))
                ->label('Partenaire'),

            TextColumn::make('payment_state')
                ->formatStateUsing(function (PartnerProjectPayment $partnerProjectPayment) {
                    return $partnerProjectPayment->payment_state->displayName();
                })
                ->weight('semibold')
                ->icon(function (PartnerProjectPayment $partnerProjectPayment) {
                    return match ($partnerProjectPayment->payment_state) {
                        PaymentStateEnum::Draft => 'heroicon-s-pencil-square',
                        PaymentStateEnum::Scheduled => 'heroicon-s-clock',
                        PaymentStateEnum::Sent => 'heroicon-s-check-circle',
                    };
                })
                ->description(function (PartnerProjectPayment $partnerProjectPayment) {
                    return match ($partnerProjectPayment->payment_state) {
                        PaymentStateEnum::Draft, PaymentStateEnum::Sent => null,
                        PaymentStateEnum::Scheduled => $partnerProjectPayment->scheduled_at?->format('H:i d/m/Y'),
                    };
                })
                ->color(function (PartnerProjectPayment $partnerProjectPayment) {
                    return match ($partnerProjectPayment->payment_state) {
                        PaymentStateEnum::Draft => 'warning',
                        PaymentStateEnum::Scheduled => 'info',
                        PaymentStateEnum::Sent => 'success',
                    };
                })
                ->label('Statut'),

            TextColumn::make('amount')
                ->suffix('€ TTC')
                ->sortable()
                ->label('Montant'),

            TextColumn::make('createdBy.name')
                ->label('Enregistré par ')
                ->default('Inconnu')
                ->description(function (PartnerProjectPayment $partnerProjectPayment): ?string {
                    return $partnerProjectPayment->created_at->format('d/m/Y');
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
        return view('livewire.tables.partner-project-payments.index-table');
    }
}
