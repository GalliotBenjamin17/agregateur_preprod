<?php

namespace App\Http\Livewire\Forms;

use App\Models\File;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class FilesForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public File $file;

    public function mount()
    {
        $this->form->fill([
            'file' => $this->file->toArray(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('file.name')
                ->label('Nom du fichier')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public function submit()
    {
        $this->file->update($this->form->getState()['file']);

        Notification::make()
            ->title('Le fichier a été mis à jour.')
            ->body('Les informations ont été modifiée et seront mises à jour sur tout le logiciel dans peu de temps.')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.forms.files-form');
    }
}
