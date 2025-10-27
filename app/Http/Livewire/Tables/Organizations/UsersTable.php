<?php

namespace App\Http\Livewire\Tables\Organizations;

use App\Enums\Roles;
use App\Models\Organization;
use App\Models\User;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UsersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Organization $organization;

    public bool $compact = false;

    protected $listeners = [
        'usersAdded' => 'render',
    ];

    protected function getTableQuery(): Builder|Relation
    {
        return User::withWhereHas('organizations', function ($query) {
            return $query->where('id', $this->organization->id);
        })->withCount('projects');
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Modifier')
                ->visible(! $this->compact)
                ->mountUsing(function (ComponentContainer $form, User $record) {
                    $record->load('organizations');

                    $organizationPivot = $record->organizations->where('id', $this->organization->id)->first()->pivot;

                    $form->fill([
                        'is_organization_manager' => $organizationPivot->is_organization_manager,
                    ]);
                })
                ->action(function (Model $record, array $data): void {
                    DB::table('user_organizations')->where('organization_id', $this->organization->id)
                        ->where('user_id', $record->id)
                        ->update([
                            'is_organization_manager' => $data['is_organization_manager'],
                        ]);
                })
                ->form([
                    Toggle::make('is_organization_manager')
                        ->inline()
                        ->onColor('success')
                        ->label("Est gérant de l'organisation")
                        ->helperText("Le gérant peut ajouter des personnes à l'organisation."),
                ])
                ->size('sm')
                ->modalSubmitActionLabel('Mettre à jour')
                ->modalHeading("Mise à jour de l'utilisateur sur l'organisation"),

            Action::make('send_link')
                ->label('Email de configuration')
                ->requiresConfirmation()
                ->visible(! $this->compact)
                ->modalHeading("Envoyer l'email de configuration de compte ?")
                ->modalDescription("L'utilisateur recevra sur son email un lien pour accéder à la page de configuration de son compte.")
                ->modalSubmitActionLabel('Envoyer')
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
                ->visible(! $this->compact)
                ->action(function (User $user) {
                    $this->organization->users()->detach($user);

                    if ($user->organizations()->count() == 0) {
                        $user->removeRole(Roles::Member);
                    }

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
                    if ($this->compact) {
                        return null;
                    }

                    return $record->roles
                        ->pluck('name')
                        ->map(fn ($value) => \Arr::get(Roles::toSelect(), $value))
                        ->join(', ', ' et ');
                })
                ->sortable(['first_name', 'last_name'])
                ->searchable(['first_name', 'last_name']),

            IconColumn::make('id')
                ->icon(function (User $record) {
                    $organization = $record->organizations->first();

                    if ($organization->pivot->is_organization_manager) {
                        return 'heroicon-o-check-circle';
                    }

                    return 'heroicon-o-x-circle';
                })
                ->label('Gestionnaire')
                ->color(function (User $record) {
                    $organization = $record->organizations->first();

                    if ($organization->pivot->is_organization_manager) {
                        return 'success';
                    }

                    return 'danger';
                }),

            TextColumn::make('projects_count')
                ->label('Projets impliqués')
                ->visible(! $this->compact)
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

    protected function isTablePaginationEnabled(): bool
    {
        return ! $this->compact;
    }

    public function render()
    {
        return view('livewire.tables.organizations.users-table');
    }
}
