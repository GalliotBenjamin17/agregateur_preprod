<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Models\Project;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use App\Notifications\Projects\NewProjectDocumentNotification;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Organization;
use Livewire\Component;

class ContributorsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Project $project;

    public function mount()
    {
        $this->form->fill([
            'project' => $this->project->toArray(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [

            Repeater::make('project.contributors_files')
                ->label('Documents pour communiquer sur leur contribution')
                ->columns(2)
                ->addActionLabel('Ajouter une ressource')
                ->schema([

                    Select::make('type')
                        ->label('Titre')
                        ->reactive()
                        ->options([
                            'link' => 'Lien externe',
                            'file' => 'Fichier',
                        ]),

                    TextInput::make('title')
                        ->required()
                        ->label('Titre de la ressource'),

                    TextInput::make('link')
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('type') == 'link')
                        ->required(fn (Get $get): bool => $get('type') == 'link')
                        ->url()
                        ->label('Lien'),

                    FileUpload::make('file')
                        ->disk('public')
                        ->openable()
                        ->downloadable()
                        ->preserveFilenames()
                        ->visible(fn (Get $get): bool => $get('type') == 'file')
                        ->required(fn (Get $get): bool => $get('type') == 'file')
                        ->label('Fichier')
                        ->maxSize(20000),

                ]),

        ];
    }

    public function submit()
    {

        $oldContributorsFiles = $this->project->contributors_files ?? [];

        $toBeUpdated = $this->form->getState()['project'];
        $state = $this->form->getState();
        $toBeUpdated = $state['project'];

        $this->project->update($toBeUpdated);
        $this->project->refresh();

        $newContributorsFiles = $this->project->contributors_files ?? [];

        if (count($newContributorsFiles) > count($oldContributorsFiles)) {
            $newlyAddedFileTitles = [];

            // Creates a map of old files for a quick search based on a unique signature
            $oldFilesMap = collect($oldContributorsFiles)->mapWithKeys(function ($file) {
                // Ensures that $file is an array, as it comes from a JSON attribute
                if (!is_array($file)) return [null => null];
                $key = ($file['title'] ?? '') . '|' . ($file['type'] ?? '') . '|' . (($file['type'] ?? '') === 'link' ? ($file['link'] ?? '') : ($file['file'] ?? ''));
                return [$key => $file];
            })->filter()->all(); // filter() to remove any null entries

            foreach ($newContributorsFiles as $newFile) {
                if (!is_array($newFile)) continue;
                $newKey = ($newFile['title'] ?? '') . '|' . ($newFile['type'] ?? '') . '|' . (($newFile['type'] ?? '') === 'link' ? ($newFile['link'] ?? '') : ($newFile['file'] ?? ''));
                if (!array_key_exists($newKey, $oldFilesMap)) {
                    if (!empty($newFile['title'])) {
                        $newlyAddedFileTitles[] = $newFile['title'];
                    }
                }
            }

            if (!empty($newlyAddedFileTitles)) {
                // Retrieving contributors (users)
                $projectDonations = $this->project->donationSplits()
                    ->with('donation')
                    ->get()
                    ->pluck('donation')
                    ->filter() // remove any null entries
                    ->unique('id');

                $contributorUsers = collect();

                // Users who contributed directly
                $directUserIds = $projectDonations->where('related_type', User::class)->pluck('related_id')->filter()->unique();
                if ($directUserIds->isNotEmpty()) {
                    $contributorUsers = $contributorUsers->merge(User::whereIn('id', $directUserIds)->get());
                }

                // Users who are members of contributing organizations
                $organizationIds = $projectDonations->where('related_type', Organization::class)->pluck('related_id')->filter()->unique();
                if ($organizationIds->isNotEmpty()) {
                    $organizations = Organization::with('users')->whereIn('id', $organizationIds)->get();
                    foreach ($organizations as $organization) {
                        $contributorUsers = $contributorUsers->merge($organization->users);
                    }
                }

                $contributors = $contributorUsers->unique('id')->values();

                if ($contributors->isNotEmpty()) {
                    foreach ($contributors as $contributorUser) {
                        if ($contributorUser instanceof User && method_exists($contributorUser, 'notify')) {
                            // Sends email notification to contributors
                            //désactivation temporaire (à reactiver après evo gestion fichiers contribution)
                            // $contributorUser->notify(new NewProjectDocumentNotification($this->project, $newlyAddedFileTitles));
                        }
                    }
                }
            }
        }

        defaultSuccessNotification('Informations mises à jour.');

    }

    public function render()
    {
        return view('livewire.forms.projects.contributors-form');
    }
}
