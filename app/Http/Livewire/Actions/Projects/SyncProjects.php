<?php

namespace App\Http\Livewire\Actions\Projects;

use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Livewire\Component;

class SyncProjects extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?Tenant $tenant = null;

    public function __construct()
    {
        if (is_null($this->tenant) and userHasTenant()) {
            $this->tenant = request()->user()->tenant;
        }
    }

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Synchroniser')
            ->color(Color::Gray)
            ->icon('heroicon-m-arrow-path')
            ->iconPosition(IconPosition::Before)
            ->size('sm')
            ->requiresConfirmation()
            ->modalDescription(function () {
                if (userHasTenant()) {
                    return 'Cela mettra à jour les projets sur les pages projets du site.';
                }

                return 'Cela mettra à jour les projets sur les pages projets de toutes les instances';
            })
            ->visible(function () {
                if ($this->tenant) {
                    return ! is_null($this->tenant->webhook_project_update);
                }

                return Tenant::whereNotNull('webhook_project_update')->exists();
            })
            ->form([

                Select::make('tenant_id')
                    ->label('Antenne locale')
                    ->visible(is_null($this->tenant))
                    ->required(is_null($this->tenant))
                    ->searchable()
                    ->options(Tenant::whereNotNull('webhook_project_update')->pluck('name', 'id')->toArray()),

            ])
            ->action(function (array $data) {

                $tenant = match (is_null($this->tenant)) {
                    true => Tenant::find($data['tenant_id']),
                    false => $this->tenant
                };

                $tenant->syncProjectsWithWebhook();

                Notification::make()
                    ->title('Demande de synchronisation effectuée sur le site : '.$tenant->public_url)
                    ->success()
                    ->send();

            });
    }

    public function render()
    {
        return view('livewire.actions.projects.sync-projects');
    }
}
