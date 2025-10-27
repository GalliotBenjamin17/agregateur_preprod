<?php

namespace App\Http\Livewire\Actions\Organizations;

use App\Enums\Roles;
use App\Models\Organization;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class LinkUsers extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Organization $organization;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajouter des utilisateurs existants')
            ->size('sm')
            ->form([
                Grid::make()
                    ->schema([
                        Select::make('users_id')
                            ->label('Utilisateurs')
                            ->searchable()
                            ->multiple()
                            ->preload()
                            ->columnSpanFull()
                            ->required()
                            ->getOptionLabelFromRecordUsing(function (User $user) {
                                return "$user->name - $user->email";
                            })
                            ->relationship('users', 'first_name', function ($query) {
                                return $query->when(userHasTenant(), function ($query) {
                                    return $query->where('tenant_id', userTenantId());
                                })->role([Roles::Auditor, Roles::Referent, Roles::Partner, Roles::Contributor, Roles::Subscriber]);
                            }),

                    ]),
            ])
            ->action(function (Component $livewire, array $data) {

                $this->organization->users()->syncWithoutDetaching($livewire->mountedActionsData[0]['users_id']);

                $users = User::whereIn('id', $livewire->mountedActionsData[0]['users_id'])->get();

                foreach ($users as $user) {
                    $user->assignRole(Roles::Member);
                }

                $this->dispatch('usersAdded');

                Notification::make('send')
                    ->title('Utilisateurs ajoutés à cette organisation')
                    ->success()
                    ->send();
            })
            ->model(Organization::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.organizations.link-users');
    }
}
