<?php

namespace App\Http\Livewire\Actions\FinancialExports;

use App\Exports\ProjectFinancials\ProjectFinancialExport;
use App\Models\Project;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class CreateForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Project $project = null;

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Effectuer un export')
            ->size('sm')
            ->icon('heroicon-m-inbox-arrow-down')
            ->requiresConfirmation()
            ->form([
                Placeholder::make('informations')
                    ->label(new HtmlString("<span class='font-bold'>Informations</span>"))
                    ->content('Cet export sera figé dans le temps et disponible à nouveau dans le tableau sur cette même page.'),
            ])
            ->action(function (Component $livewire, array $data) {
                $fileName = 'financial-exports/Export financier - '.now()->timestamp.'.xlsx';

                (new ProjectFinancialExport(tenant: Tenant::first()))->store(
                    $fileName,
                    'public');

                \App\Models\ProjectFinancialExport::create([
                    'file_path' => $fileName,
                    'generated_at' => now(),
                    'generated_by' => request()->user()->id,
                ]);

                $this->dispatch('financialExportAdded');

            })
            ->slideOver();
    }

    public function render()
    {
        return view('livewire.actions.financial-exports.create-form');
    }
}
