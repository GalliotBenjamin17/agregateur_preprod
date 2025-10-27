<?php

namespace App\Http\Livewire\Actions\Projects;

use App\Models\File;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;

class GenerateAnnualReport extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Project $project;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Générer ler rapport annuel')
            ->color(Color::Gray)
            ->icon('heroicon-m-arrow-path')
            ->iconPosition(IconPosition::Before)
            ->size('sm')
            ->requiresConfirmation()
            ->form([

                TextInput::make('url')
                    ->label('URL de la page publique')
                    ->required()
                    ->helperText("Le ?print=1 sera automatiquement ajouté à la fin de l'url."),

            ])
            ->action(function (array $data) {

                Browsershot::url(
                    $data['url'].'?print=1'
                )
                    ->format('A4')
                    ->delay(2000)
                    ->margins(10, 0, 10, 0)
                    ->showBackground()
                    ->savePdf(storage_path('app/public/'.\Str::slug($this->project->name).'-'.now()->format('d-m-Y').'.pdf'));

                $file = File::create([
                    'name' => 'Rapport annuel '.now()->format('Y'),
                    'related_id' => $this->project->id,
                    'related_type' => get_class($this->project),
                    'path' => \Str::slug($this->project->name).'-'.now()->format('d-m-Y').'.pdf',
                    'created_by' => request()->user()->id,
                    'extension' => 'pdf',
                    'content_type' => 'application/pdf',
                ]);

                $this->project->update([
                    'annual_report_file_id' => $file->id,
                ]);

                defaultSuccessNotification('Rapport généré et ajouté aux fichiers du projet.', "Si vous souhaitez le rendre disponible sur l'interface, téléchargez le et ajouter le en tant que document ci-dessous.");

            });
    }

    public function render()
    {
        return view('livewire.actions.projects.generate-annual-report');
    }
}
