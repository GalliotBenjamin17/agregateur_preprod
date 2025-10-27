<?php

namespace App\Http\Livewire\Forms\Donations;

use App\Helpers\ActivityHelper;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\User;
use App\Traits\Filament\HasDataState;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class DonationsForm extends Component implements HasActions, HasForms
{
    use HasDataState, InteractsWithForms {
        HasDataState::getFormStatePath insteadof InteractsWithForms;
    }
    use InteractsWithActions;

    public Donation $donation;

    public array $usersSponsor = [];

    public array $organizationSponsor = [];

    public function mount()
    {
        $this->organizationSponsor = Organization::select(['id', 'name'])
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->usersSponsor = User::select(['id', 'first_name', 'last_name', 'tenant_id'])
            ->tenantable()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->form->fill([
            'donation' => $this->donation->toArray(),
            'bill_file' => str_replace('/storage/', '', $this->donation->bill_file),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('sponsor')
                ->label('Provenance de la contribution')
                ->schema([
                    Select::make('donation.related_type')
                        ->label('Type de donateur')
                        ->disabled()
                        ->dehydrated()
                        ->options([
                            Organization::class => 'Organisation',
                            User::class => 'Particulier',
                        ])->reactive(),

                    Select::make('donation.related_id')
                        ->label('Organisation')
                        ->dehydrated()
                        ->disabled()
                        ->visible(fn (\Filament\Forms\Get $get) => $get('donation.related_type') == Organization::class)
                        ->options($this->organizationSponsor),

                    Select::make('donation.related_id')
                        ->label('Particulier')
                        ->visible(fn (\Filament\Forms\Get $get) => $get('donation.related_type') == User::class)
                        ->options($this->usersSponsor),
                ]),

            Fieldset::make('transaction_infos')
                ->label('Informations de transactions')
                ->schema([
                    TextInput::make('donation.bill_reference')
                        ->label('Numéro de facture'),

                    FileUpload::make('bill_file')
                        ->openable()
                        ->downloadable()
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            return formatFileName($file->getClientOriginalName());
                        })
                        ->label('Facture'),

                    TextInput::make('donation.external_id')
                        ->disabled()
                        ->label('Numéro de transaction')
                        ->required()
                        ->placeholder('AF6716....'),

                    TextInput::make('donation.amount')
                        ->label('Montant de la transaction')
                        ->disabled()
                        ->placeholder('500')
                        ->numeric()
                        ->suffix('€'),
                ]),
        ];
    }

    public function submit()
    {
        $this->donation->update(array_merge($this->form->getState()['donation'], [
            'bill_file' => '/storage/'.$this->form->getState()['bill_file'],
        ]));

        Notification::make()
            ->title('Contribution mise à jour.')
            ->success()
            ->send();

        ActivityHelper::push(
            performedOn: $this->donation,
            title: 'Mise à jour de la contribution',
            url: route('donations.show.details', ['donation' => $this->donation])
        );
    }

    public function render()
    {
        return view('livewire.forms.donations.donations-form');
    }
}
