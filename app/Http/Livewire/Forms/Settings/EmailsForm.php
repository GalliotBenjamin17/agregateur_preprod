<?php

namespace App\Http\Livewire\Forms\Settings;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Livewire\Component;

class EmailsForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $minDaySlot;

    public function mount()
    {
        $settings = setting()->all();

        $this->form->fill([
            'welcome_email_content' => Arr::get($settings, 'welcome_email_content', "Vous avez été ajouté en tant qu'utilisateur sur la plateforme. Configurez votre compte en cliquant sur le bouton ci-dessous pour accéder à votre tableau de bord."),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('slots')
                ->label("Email d'accueil")
                ->schema([
                    RichEditor::make('welcome_email_content')
                        ->label("Corps de l'email d'accueil")
                        ->disableToolbarButtons([
                            'attachFiles',
                            'points',
                            'number',
                            'codeBlock',
                            'h2',
                            'h3',
                            'attachFiles',
                            'blockquote',
                            'strike',
                        ])
                        ->required()
                        ->columnSpanFull()
                        ->lazy(),
                ]),
        ];
    }

    public function submit(): void
    {
        setting([
            'welcome_email_content' => $this->form->getState()['welcome_email_content'],
        ])->save();

        Notification::make()
            ->title("Les paramètres d'emails ont été mise à jour.")
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.forms.settings.variables-form');
    }
}
