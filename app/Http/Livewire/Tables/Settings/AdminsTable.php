<?php

namespace App\Http\Livewire\Tables\Settings;

use App\Enums\Roles;
use App\Models\Tenant;
use App\Models\User;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
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
use Livewire\Component;

class AdminsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return User::when(request()->user()->hasRole(Roles::Admin), function ($query) {
            return $query->role([
                Roles::Admin,
                Roles::LocalAdmin,
            ]);
        })->when(request()->user()->hasRole(Roles::LocalAdmin), function ($query) {
            return $query->role([
                Roles::LocalAdmin,
            ])->where('tenant_id', userTenantId());
        });
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('send_link')
                ->label('Lien de connexion')
                ->action(function (User $record) {
                    $record->sendWelcomeNotification(now()->addMonth(), isMigration: true, isRegister: false);

                    Notification::make()
                        ->success()
                        ->title('Email envoyé')
                        ->send();
                }),

            Action::make('Modifier')
                ->visible(function (User $record) {
                    return $record->hasRole(Roles::Admin);
                })
                ->mountUsing(function (ComponentContainer $form, Model $record) {
                    $form->fill([
                        'first_name' => $record->first_name,
                        'last_name' => $record->last_name,
                        'email' => $record->email,
                        'tenant_id' => $record->tenant_id,
                        'role' => $record->roles->pluck('name')->toArray()[0] ?? null,
                    ]);
                })
                ->action(function (User $record, array $data): void {
                    $record->update([
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'email' => $data['email'],
                        'tenant_id' => $data['role'] == Roles::LocalAdmin ? $data['tenant_id'] : null,
                    ]);

                    $record->syncRoles($data['role']);
                })
                ->form([
                    Grid::make(2)->schema(components: [
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->required(),
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->columnSpanFull()
                            ->required(),
                        Select::make('role')
                            ->placeholder('Sélectionnez un ou plusieurs rôles')
                            ->selectablePlaceholder(false)
                            ->options([
                                Roles::Admin => 'Administrateur national',
                                Roles::LocalAdmin => 'Administrateur local',
                            ])
                            ->reactive()
                            ->label('Rôle'),
                        Select::make('tenant_id')
                            ->reactive()
                            ->required(fn (\Filament\Forms\Get $get) => $get('role') == Roles::LocalAdmin)
                            ->visible(fn (\Filament\Forms\Get $get) => $get('role') == Roles::LocalAdmin)
                            ->placeholder('Sélectionnez une antenne locale')
                            ->options(Tenant::all()->pluck('name', 'id'))
                            ->label('Antenne locale'),

                    ]),
                ])
                ->modalHeading("Mise à jour de l'administrateur")
                ->modalSubmitActionLabel('Mettre à jour'),

            Action::make('Supprimer')
                ->action(function (Model $record, array $data): void {
                    $record->delete();
                })
                ->visible(function (Model $record) {
                    return $record->id != request()->user()->id;
                })
                ->requiresConfirmation()
                ->color('danger'),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->description(function (User $record) {
                    return $record->email ?? 'Aucun email disponible';
                })
                ->searchable(['first_name', 'last_name']),

            TextColumn::make('id')
                ->label('Rôle')
                ->formatStateUsing(function (User $record) {
                    return \Arr::get(Roles::toDisplay(), $record->roles->pluck('name')->toArray()[0]);
                }),

        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [25, 50, 100];
    }

    public function render()
    {
        return view('livewire.tables.settings.admins-table');
    }
}
