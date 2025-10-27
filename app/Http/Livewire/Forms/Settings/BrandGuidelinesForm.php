<?php

namespace App\Http\Livewire\Forms\Settings;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BrandGuidelinesForm extends Component implements HasForms
{
    use InteractsWithForms;

    public function mount()
    {
        $settings = setting()->all();

        $this->form->fill([
            'name' => Arr::get($settings, 'name'),
            'brand_color' => Arr::get($settings, 'brand_color'),
            'text_color' => Arr::get($settings, 'text_color'),
            'contact_phone' => Arr::get($settings, 'contact_phone'),
            'logo' => str_replace('/storage/', '', $settings['logo'] ?? ''),
            'login_image' => str_replace('/storage/', '', $settings['login_image'] ?? ''),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('informations')
                ->label('Informations globale')
                ->schema([
                    TextInput::make('name')
                        ->label('Nom public')
                        ->required()
                        ->lazy()
                        ->columnSpanFull(),
                    TextInput::make('contact_phone')
                        ->label('Téléphone de contact')
                        ->tel()
                        ->lazy()
                        ->placeholder('07.86.89.00.05')
                        ->mask(fn ($mask) => $mask->pattern('00.00.00.00.00')),
                    ColorPicker::make('brand_color')
                        ->label('Couleur de la marque'),
                    ColorPicker::make('text_color')
                        ->label('Couleur du texte'),
                ]),
            Fieldset::make('informations')
                ->label('Informations visuelle')
                ->schema([
                    FileUpload::make('logo')
                        ->label('Logo')
                        ->visibility('public')
                        ->image()
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->preserveFilenames()
                        ->lazy(),
                    FileUpload::make('login_image')
                        ->label('Page de connexion')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->image()
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->lazy(),
                ]),

        ];
    }

    public function submit(): void
    {
        Session::flash('success', 'Les paramètres ont été mis à jour.');

        setting([
            'name' => $this->form->getState()['name'],
            'brand_color' => $this->form->getState()['brand_color'],
            'text_color' => $this->form->getState()['text_color'],
            'contact_phone' => $this->form->getState()['contact_phone'],
            'logo' => '/storage/'.$this->form->getState()['logo'],
            'login_image' => '/storage/'.$this->form->getState()['login_image'],
        ])->save();

        Session::flash('success', 'Les paramètres ont été mis à jour.');
    }

    public function render()
    {
        return view('livewire.forms.settings.brand-guidelines-form');
    }
}
