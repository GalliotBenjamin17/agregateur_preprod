<?php

namespace App\Http\Livewire\Tables\Transactions;

use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Models\TransactionService;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class IndexTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
    ];

    protected function getTableQuery(): Builder
    {
        return Transaction::with([
            'related',
            'tenant',
            'createdBy',
        ]);
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->label('Statuts')
                ->multiple()
                ->options([
                    'PAID' => 'Payé',
                    'UNPAID' => 'Non payé',
                    'RUNNING' => 'En cours',
                    'PARTIALLY_PAID' => 'Payé partiellement',
                    'ABANDONED' => 'Abandonné / Expiré',
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('resent_email')
                ->label('Envoyer email')
                ->visible(function (Transaction $record) {
                    return in_array($record->status, [
                        'UNPAID',
                        'RUNNING',
                        'PARTIALLY_PAID',
                    ]);
                })
                ->mountUsing(function (ComponentContainer $form, Transaction $record) {
                    $form->fill([
                        'billing_email' => $record->related->billing_email,
                        'email_content' => "Bonjour, <br><br>Nous nous permettons de vous relancer à propos de votre contribution d'un montant de ".format($record->amount / 100, 2).'€. <br><br>Vous pouvez la régler directement ligne via le bouton ci-dessous. <br>Merci beaucoup',
                    ]);
                })
                ->action(function (Transaction $record, array $data): void {
                    try {
                        $transactionService = new TransactionService(tenant: $record->tenant);
                        $transactionService->sendEmail(transaction: $record, email: $data['billing_email'], text: $data['email_content']);
                    } catch (\Exception $e) {

                        Notification::make()
                            ->danger()
                            ->title("Impossible d'envoyer l'email")
                            ->body($e->getMessage())
                            ->send();

                    }
                })
                ->form([
                    Grid::make('2')->schema(components: [
                        TextInput::make('billing_email')
                            ->helperText("Vous pouvez mettre à jour cet email sur la page de l'organisation.")
                            ->label('Email'),

                        RichEditor::make('email_content')
                            ->disableToolbarButtons(['attachFiles', 'codeBlock', 'h2', 'h3', 'link', 'attachFiles', 'blockquote', 'strike'])
                            ->name("Contenu de l'email")
                            ->columnSpanFull(),

                    ]),
                ])
                ->modalHeading("Relance de l'organisation")
                ->modalSubmitActionLabel('Envoyer'),
        ];
    }

    protected function getTableRecordUrlUsing(): \Closure
    {
        return fn (Transaction $record): string => $record->payment_url;
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('related.name')
                ->label('Envoyé à ')
                ->description(function (Transaction $record): string {
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

            TextColumn::make('status_text')
                ->label('Statut')
                ->default(function (Transaction $record) {
                    return match ($record->status) {
                        'PAID' => 'Payé',
                        'UNPAID' => 'Non payé',
                        'RUNNING' => 'En cours',
                        'PARTIALLY_PAID' => 'Payé partiellement',
                        'ABANDONED' => 'Abandonné',
                    };
                })
                ->color(function (Transaction $record) {
                    return match ($record->status) {
                        'PAID' => 'success',
                        'UNPAID', 'ABANDONED' => 'danger',
                        'RUNNING' => 'primary',
                        'PARTIALLY_PAID' => 'warning',
                    };
                })->weight('semibold'),

            TextColumn::make('amount')
                ->formatStateUsing(fn ($state) => format($state / 100, 2))
                ->label('Montant')
                ->suffix('€')
                ->searchable(),

            TextColumn::make('payment_url')
                ->copyable(function (Transaction $record) {
                    return $record->status != 'PAID';
                })
                ->formatStateUsing(function (Transaction $record) {
                    if ($record->status != 'PAID') {
                        return $record->payment_url;
                    }

                    return '-';
                })
                ->copyMessage('Lien de paiement copié')
                ->copyMessageDuration(1500),

            TextColumn::make('expiration_at')
                ->label("Lien valide jusqu'au")
                ->date('d/m/Y')
                ->formatStateUsing(function (Transaction $record) {
                    if (is_null($record->donation_id)) {
                        return $record->expiration_at?->format('d/m/Y') ?? '-';
                    }

                    return '-';
                })
                ->sortable(),

            TextColumn::make('createdBy.name')
                ->label('Création')
                ->description(function (Model $record): ?string {
                    return $record->created_at->format('\A H:i \l\e d/m/Y');
                })
                ->sortable(['created_at']),
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
        return view('livewire.tables.transactions.index-table');
    }
}
