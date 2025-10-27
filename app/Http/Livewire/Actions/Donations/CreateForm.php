<?php

namespace App\Http\Livewire\Actions\Donations;

use App\Exports\ProjectFinancials\ProjectFinancialExport;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public array $organizations = [];

    public function mount()
    {
        $this->organizations = Organization::select(['id', 'name', 'billing_email'])
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

    }

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Ajout manuel')
            ->size('sm')
            ->color('gray')
            ->icon('heroicon-m-inbox-arrow-down')
            ->requiresConfirmation()
            ->mountUsing(function (Form $form) {
                $form->fill([
                    'description' => "Importé manuellement par " . request()->user()->name,
                ]);
            })
            ->form([
                Select::make('organization_id')
                    ->searchable()
                    ->label('Organisation')
                    ->required()
                    ->options($this->organizations),

                TextInput::make('amount')
                    ->label('Montant (TTC)')
                    ->required()
                    ->numeric()
                    ->suffix(' €'),

                TextInput::make('description')
                    ->disabled()
                    ->label('Description'),



            ])
            ->action(function (Component $livewire, array $data) {

                /** @var Organization $organization */
                $organization = Organization::findOrFail($data['organization_id']);

                $donation = Donation::create([
                    'tenant_id' => $organization->tenant_id,
                    'related_type' => get_class($organization),
                    'related_id' => $organization->id,
                    'source' => 'bank_account',
                    'external_id' => "manual_import",
                    'amount' => $data['amount'],
                    'created_at' => now(),
                    'description' => "Importé manuellement par " . request()->user()->name,
                    'created_by' => request()->user()->id,
                ]);

                defaultSuccessNotification(title: "Transaction importée dans la fiche de l'organisation.");

            })
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.donations.create-form');
    }
}
