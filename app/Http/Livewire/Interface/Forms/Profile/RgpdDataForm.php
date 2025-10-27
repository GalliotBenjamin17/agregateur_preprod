<?php

namespace App\Http\Livewire\Interface\Forms\Profile;

use App\Models\User;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Str;
use Livewire\Component;

class RgpdDataForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public function mount()
    {
        $this->form->fill([
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Portabilité des données')
                ->compact()
                ->schema([

                    Toggle::make('confirm')
                        ->confirmed()
                        ->helperText("Les utilisateurs ont le droit de demander l'accès à leurs données personnelles. Pour exercer ce droit, veuillez envoyer une demande en soumettant le formulaire ci-dessus.")
                        ->label('Je souhaite faire une demande de récupération de mes données stockées sur ce site.'),

                ]),

        ];
    }

    public function submit()
    {
        $user = User::where('id', \Auth::id())->with([
            'organizations:id,name',
            'donations:id,source,related_type,related_id,amount',
            'comments:id,related_id,content',
            'partners:id,name',
        ])->first();

        defaultSuccessNotification(
            title: 'Vos données ont été téléchargées dans votre navigateur.',
            description: ''

        );

        return response()->streamDownload(function () use ($user) {
            echo $user;
        }, Str::slug($user->name).'.json');

    }

    public function render()
    {
        return view('livewire.interface.forms.profile.rgpd-data-form');
    }
}
