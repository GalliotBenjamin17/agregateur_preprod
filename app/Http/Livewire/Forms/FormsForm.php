<?php

namespace App\Http\Livewire\Forms;

use App\Models\Form;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Component;

class FormsForm extends Component implements HasForms
{
    use InteractsWithForms;

    public Form $formDetails;

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('internal_information')
                ->label('Informations internes')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('formDetails.name')
                            ->label('Nom du formulaire')
                            ->lazy()
                            ->required()
                            ->autofocus()
                            ->columnSpanFull(),
                        RichEditor::make('formDetails.description')
                            ->placeholder('Autres informations utiles sur le formulaire ...')
                            ->disableToolbarButtons(['attachFiles', 'codeBlock', 'h2', 'h3', 'link', 'attachFiles', 'blockquote', 'strike'])
                            ->columnSpanFull()
                            ->lazy(),
                    ]),
                ]),
            Fieldset::make('public_information')
                ->label('Informations publiques')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('formDetails.title')
                            ->label('Titre du formulaire')
                            ->helperText('Ce titre apparaît en haut du formulaire.')
                            ->placeholder('Votre avis compte pour nous')
                            ->lazy()
                            ->columnSpanFull(),
                        Textarea::make('formDetails.sub_title')
                            ->label('Sous-titre')
                            ->helperText('Ce texte apparaîtra sous le titre.')
                            ->placeholder('Autres informations utiles sur le formulaire ...')
                            ->columnSpanFull()
                            ->lazy(),
                    ]),
                ]),
            Fieldset::make('public_information_confirmation')
                ->label('Informations une fois le formulaire envoyé')
                ->schema([
                    Grid::make(2)->schema([
                        Textarea::make('formDetails.confirmation_text')
                            ->label('Message de confirmation')
                            ->helperText('Ce texte apparaîtra sur la page de confirmation de la bonne réception des réponses.')
                            ->placeholder("Nous vous remercions d'avoir participé à notre enquête ...")
                            ->columnSpanFull()
                            ->lazy(),
                    ]),
                ]),
            Fieldset::make('share')
                ->label('Informations de partage')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('formDetails.slug')
                            ->label('Url de partage')
                            ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                $set('formDetails.slug', Str::slug($state));
                            }),
                        Toggle::make('formDetails.auth_required')
                            ->label("L'utilisateur doit être connecté pour répondre au questionnaire.")
                            ->columnSpanFull(),
                    ]),
                ]),
        ];
    }

    public function submit()
    {
        $this->formDetails->update($this->form->getState()['formDetails']);

        Session::flash('success', 'Le formulaire a été mis à jour.');

        return to_route('forms.show.details', ['form' => $this->formDetails->slug]);
    }

    public function render()
    {
        return view('livewire.forms.forms-form');
    }
}
