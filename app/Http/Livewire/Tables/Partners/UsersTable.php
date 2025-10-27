<?php

namespace App\Http\Livewire\Tables\Partners;

use App\Enums\Roles;
use App\Models\Partner;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Locked;
use Livewire\Component;

class UsersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public Partner $partner;

    protected $listeners = [
        'usersAdded' => 'render',
    ];

    protected function getTableQuery(): Builder|Relation
    {
        return User::whereRelation('partners', 'id', '=', $this->partner->id);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('send_link')
                ->label('Email de configuration')
                ->requiresConfirmation()
                ->modalHeading("Envoyer l'email de configuration de compte ?")
                ->modalDescription("L'utilisateur recevra sur son email un lien pour accéder à la page de configuration de son compte.")
                ->modalSubmitActionLabel('Envoyer')
                ->slideOver()
                ->action(function (User $record): void {
                    $record->sendWelcomeNotification(now()->addYear());

                    Notification::make()
                        ->title('Email envoyé')
                        ->body("L'email pour se reconnecter à été envoyé à l'utilisateur.")
                        ->success()
                        ->send();

                }),

            Action::make('detach')
                ->color('danger')
                ->label('Détacher')
                ->action(function (User $user) {
                    $this->partner->users()->detach($user);

                    $user->removeRole(Roles::Partner);

                    $user->touch();

                    Notification::make()
                        ->title('Utilisateur détaché')
                        ->body("L'utilisateur a été détaché de cette organisation et n'aura plus accès aux informations de cette dernière.")
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nom')
                ->description(function (User $record) {
                    return $record->roles
                        ->pluck('name')
                        ->map(fn ($value) => \Arr::get(Roles::toSelect(), $value))
                        ->join(', ', ' et ');
                })
                ->sortable(['first_name', 'last_name'])
                ->searchable(['first_name', 'last_name']),

            TextColumn::make('projects_count')
                ->label('Projets impliqués')
                ->counts('projects')
                ->sortable(),

            TextColumn::make('created_at')
                ->label("Date d'inscription")
                ->date('d/m/Y')
                ->sortable(),
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
        return view('livewire.tables.partners.users-table');
    }
}
