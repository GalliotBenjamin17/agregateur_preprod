<?php

namespace App\Http\Livewire\Interface\Forms;

use App\Models\Tenant;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class DonationChoiceForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Tenant $tenant;

    public float $amount;

    public ?int $projectId = null;

    public ?int $wpPageId = null;

    public $organizations;

    public function mount(): void
    {
        $this->organizations = request()->user()->organizations;
    }

    public function chooseAction(): Action
    {
        $organizations = $this->organizations;
        $amount = $this->amount;

        return Action::make('choose')

            ->modalHeading(new HtmlString("<div class='text-center text-bold text-lg'>Attribution de la contribution</div>"))
            ->modalDescription(new HtmlString("<p class='text-center text-gray-500 text-sm text-balance font-semibold'>Veuillez choisir si vous souhaitez contribuer à hauteur de {$amount}€ à titre personnel ou au titre d'une organisation</p>"))
            ->modalWidth('lg')
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalCloseButton(false)
            ->modalCancelAction(false)
            ->modalSubmitAction(fn($action) => $action
                ->color('success')
                ->extraAttributes([
                    'class' => 'w-full'
                ], merge: true)
                ->label('Contribuer')
            )
            ->form([

                ToggleButtons::make('donation_type')
                    ->label('Type de contribution')
                    ->extraAttributes([
                        'class' => 'w-full'
                    ], merge: true)
                    ->options([
                        'individual' => 'Particulier',
                        'organization' => 'Organisation',
                    ])
                    ->icons([
                        'individual' => 'heroicon-o-user',
                        'organization' => 'heroicon-o-building-office',
                    ])
                    //->grouped()
                    ->inline()
                    ->default('individual')
                    ->columnSpanFull()
                    ->reactive()
                    ->required(),

                Select::make('organization_id')
                    ->label('Sélectionnez l\'organisation')
                    ->options($organizations->pluck('name', 'id'))
                    ->visible(fn ($get) => $get('donation_type') === 'organization' && $organizations->count() > 1)
                    ->required(fn ($get) => $get('donation_type') === 'organization' && $organizations->count() > 1),

                Placeholder::make('organization_name')
                    ->label('Organisation')
                    ->content(fn () => "Vous contribuerez au nom de {$organizations->first()->name}")
                    ->visible(fn ($get) => $get('donation_type') === 'organization' && $organizations->count() === 1),
            ])
            ->action(function (array $data) {
                $queryParams = [
                    'amount' => $this->amount,
                    'donation_type' => $data['donation_type'],
                ];

                if ($this->projectId) {
                    $queryParams['projectId'] = $this->projectId;
                }

                $queryParams['tenantId'] = $this->tenant->id;

                if ($this->wpPageId) {
                    $queryParams['wpPageId'] = $this->wpPageId;
                }

                if ($data['donation_type'] === 'organization') {
                    $organizationId = $this->organizations->count() === 1
                        ? $this->organizations->first()->id
                        : $data['organization_id'];

                    $queryParams['organizationId'] = $organizationId;
                }

                $this->redirect(route('api.donation.redirect-auth', array_merge([
                    'tenant' => $this->tenant->domain,
                ], $queryParams)));
            });
    }

    public function render()
    {
        return view('livewire.interface.forms.donation-choice-form');
    }
}
