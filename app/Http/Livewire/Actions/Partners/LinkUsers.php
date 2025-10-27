<?php

namespace App\Http\Livewire\Actions\Partners;

use App\Enums\Roles;
use App\Models\Partner;
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

    public Partner $partner;

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
                                })
                                    ->role([Roles::Auditor, Roles::Referent, Roles::Partner]);
                            }),

                    ]),
            ])
            ->action(function (Component $livewire, array $data) {

                $this->partner->users()->syncWithoutDetaching($livewire->mountedActionsData[0]['users_id']);

                $users = User::whereIn('id', $livewire->mountedActionsData[0]['users_id'])->get();

                foreach ($users as $user) {
                    $user->assignRole(Roles::Partner);
                }

                $this->dispatch('usersAdded');

                Notification::make('send')
                    ->title('Utilisateurs ajoutés à ce partenaire.')
                    ->success()
                    ->send();
            })
            ->model(Partner::class)
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.partners.link-users');
    }
}
