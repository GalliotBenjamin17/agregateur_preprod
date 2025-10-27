<?php

namespace App\Http\Livewire\Forms\Settings;

use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Livewire\Component;

class VariablesForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public function mount()
    {
        $settings = setting()->all();

        $this->form->fill([
            'price_tco2' => Arr::get($settings, 'price_tco2'),
            'welcome_explanations' => Arr::get($settings, 'welcome_explanations', 'Vous avez reçu un lien de configuration de compte pour vous connecter sur la plateforme.'),
            'gdpr_explanations' => Arr::get($settings, 'gdpr_explanations', "<p>Pour accéder à la plateforme, vous devez consentir à l'utilisation de vos données et aux traitements futurs de vos données personnelles.</p>"),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('variables')
                ->label('Variables plateforme')
                ->schema([
                    TextInput::make('price_tco2')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(.01)
                        ->helperText("Ce prix sera automatiquement affilié à un nouveau projet si aucun prix n'est défini sur l'instance locale.")
                        ->suffix('€ HT / tonne')
                        ->label('Prix HT tonne CO2')
                        ->lazy(),
                ]),

            Fieldset::make('welcome_settings')
                ->label("Paramètres d'accueil")
                ->schema([
                    Textarea::make('welcome_explanations')
                        ->rows(2)
                        ->required()
                        ->columnSpanFull()
                        ->label("Phrase d'explication sur la page de configuration du compte.")
                        ->lazy(),
                ]),
            Fieldset::make('gdpr_settings')
                ->label('Paramètres RGPD')
                ->schema([
                    RichEditor::make('gdpr_explanations')
                        ->required()
                        ->columnSpanFull()
                        ->disableToolbarButtons(['attachFiles', 'codeBlock', 'h2', 'h3', 'attachFiles', 'blockquote', 'strike'])
                        ->label("Phrase d'explication sur l'utilisation des données.")
                        ->lazy(),
                ]),
        ];
    }

    public function submit(): void
    {
        setting($this->form->getState())->save();

        Notification::make()
            ->title("Variables mises à jour sur l'ensemble de la plateforme.")
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.forms.settings.variables-form');
    }
}
