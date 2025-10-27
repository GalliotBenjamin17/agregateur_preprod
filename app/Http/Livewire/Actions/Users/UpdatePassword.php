<?php

namespace App\Http\Livewire\Actions\Users;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Locked;
use Livewire\Component;

class UpdatePassword extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Locked]
    public User $user;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Mettre à jour mon mot de passe')
            ->color(Color::Blue)
            ->icon('heroicon-m-lock-closed')
            ->iconPosition(IconPosition::Before)
            ->size(ActionSize::ExtraSmall)
            ->slideOver()
            ->form([

                TextInput::make('current_password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->label('Mot de passe actuel'),

                Fieldset::make('new_password')
                    ->label('Nouveau mot de passe')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->confirmed()
                            ->rule(Password::default())
                            ->validationAttribute('nouveau mot de passe')
                            ->label('Mot de passe'),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->required()
                            ->validationAttribute('')
                            ->label('Confirmation'),
                    ]),
            ])
            ->action(function (array $data) {

                if (! Hash::check($data['current_password'], request()->user()->password)) {
                    Notification::make()
                        ->title('Vot de passe actuel non valide')
                        ->danger()
                        ->send();

                    return;
                }

                request()->user()->update([
                    'password' => Hash::make($data['password']),
                ]);

                defaultSuccessNotification('Mot de passe mis à jour');

            });
    }

    public function render()
    {
        return view('livewire.actions.users.update-password');
    }
}
