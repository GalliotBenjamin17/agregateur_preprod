<?php

namespace App\Http\Livewire\Forms\ProjectPartners;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Helpers\ActivityHelper;
use App\Models\PartnerProject;
use App\Services\Models\PartnerProjectService;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class DetailsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public PartnerProject $partnerProject;

    public function mount()
    {
        $this->form->fill([
            'partnerProject' => $this->partnerProject->toArray(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Select::make('partnerProject.commission_type')
                        ->label('Type de commission')
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->options(CommissionTypeEnum::toArray()),

                    TextInput::make('partnerProject.commission_percentage')
                        ->label('Pourcentage de la commission')
                        ->suffix('%')
                        ->numeric()
                        ->reactive()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(fn (Get $get) => CommissionTypeEnum::tryFrom($get('partnerProject.commission_type')) == CommissionTypeEnum::Percentage)
                        ->visible(fn (Get $get) => CommissionTypeEnum::tryFrom($get('partnerProject.commission_type')) == CommissionTypeEnum::Percentage),

                    TextInput::make('partnerProject.commission_numerical')
                        ->label('Montant de la commission ( € HT)')
                        ->suffix('€ HT')
                        ->numeric()
                        ->reactive()
                        ->minValue(0)
                        ->required(fn (Get $get) => CommissionTypeEnum::tryFrom($get('partnerProject.commission_type')) == CommissionTypeEnum::Numerical)
                        ->visible(fn (Get $get) => CommissionTypeEnum::tryFrom($get('partnerProject.commission_type')) == CommissionTypeEnum::Numerical),
                ]),
        ];
    }

    public function submit()
    {
        $partnerProjectService = new PartnerProjectService(partnerProject: $this->partnerProject);
        $partnerProjectService->update([
            ...$this->form->getState()['partnerProject'],
        ]);

        Notification::make()
            ->title('Le partenaire a été mis à jour')
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->partnerProject->project,
            title: 'Partenaire mis à jour',
            url: route('projects.show.partners.details', ['project' => $this->partnerProject->project, 'partnerProject' => $this->partnerProject])
        );
    }

    protected function getFormModel(): Model|string|null
    {
        return $this->partnerProject;
    }

    public function render()
    {
        return view('livewire.forms.project-partners.details-form');
    }
}
