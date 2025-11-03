<?php

namespace App\Http\Livewire\Forms\Projects;

use App\Models\Project;
use App\Models\Tenant;
use App\Models\Segmentation;
use App\Models\Certification;
use App\Traits\Filament\HasDataState;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class CertificationTenantForm extends Component implements HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }

    public Project $project;

    public array $tenants = [];

    public array $segmentations = [];

    public function mount()
    {
        $this->tenants = Tenant::select(['id', 'name'])
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->segmentations = Segmentation::orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->form->fill([
            'project' => $this->project->toArray(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('informations')
                ->label('Certification & instance locale')
                ->disabled($this->project->hasFormFieldsDisabled())
                ->schema([
                    Select::make('project.tenant_id')
                        ->label('Instance locale')
                        ->searchable()
                        ->placeholder('Projet national')
                        ->disabled($this->project->hasParent())
                        ->helperText($this->project->hasParent() ? 'Vous pouvez modifier cette information sur le projet parent.' : null)
                        ->options($this->tenants),

                    Select::make('project.certification_id')
                        ->label('Certification')
                        ->searchable()
                        ->disabled($this->project->hasParent())
                        ->helperText($this->project->hasParent() ? 'Vous pouvez modifier cette information sur le projet parent.' : null)
                        ->options(function (\Filament\Forms\Get $get) {
                            return Certification::select(['id', 'name', 'tenant_id'])
                                ->whereNull('tenant_id')
                                ->orWhere('tenant_id', $get('project.tenant_id'))
                                ->get()
                                ->pluck('name', 'id')
                                ->toArray();
                        }),

                    Select::make('project.segmentation_id')
                        ->label('Segmentation')
                        ->disabled($this->project->hasParent())
                        ->required(! $this->project->hasParent())
                        ->helperText($this->project->hasParent() ? 'Vous pouvez modifier cette information sur le projet parent.' : null)
                        ->searchable()
                        ->options($this->segmentations),

                    Select::make('project.method_form_id')
                        ->label('Méthode')
                        ->searchable()
                        ->reactive()
                        ->hidden($this->project->hasParent())
                        ->disabled(! is_null($this->project->method_form_id))
                        ->helperText(! is_null($this->project->method_form_id) ? 'Vous avez déjà modifier au moins un élément de la méthode. Supprimer la méthode pour la modifier.' : 'La liste des méthodes dépend de la segmentation sélectionnée.')
                        ->options(function (\Filament\Forms\Get $get) {
                            $activeMethodFormIds = \App\Models\MethodFormGroup::where('segmentation_id', $get('project.segmentation_id'))
                                ->pluck('active_method_form_id')
                                ->toArray();

                            if (!is_null($this->project->method_form_id)) {
                                $activeMethodFormIds[] = $this->project->method_form_id;
                            }

                            return \App\Models\MethodForm::whereIn('id', $activeMethodFormIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        }),
                ]),
        ];
    }

    public function submit()
    {
        $formState = $this->form->getState();
        $payload = $formState['project'] ?? [];

        // Only keep the fields managed by this form
        $allowed = [
            'tenant_id',
            'certification_id',
            'segmentation_id',
            'method_form_id',
        ];

        $toUpdate = array_intersect_key($payload, array_flip($allowed));

        if (! empty($toUpdate)) {
            $this->project->update($toUpdate);
        }

        Notification::make()
            ->title('Informations mises à jour')
            ->success()
            ->send();

        // Rafraîchir la page Labellisation après mise à jour
        return redirect()->route('projects.show.methods-informations', [
            'project' => $this->project->slug,
        ]);
    }

    public function render()
    {
        return view('livewire.forms.projects.certification-tenant-form');
    }
}
