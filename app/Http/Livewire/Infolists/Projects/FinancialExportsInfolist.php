<?php

namespace App\Http\Livewire\Infolists\Projects;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Arr;
use Livewire\Component;

class FinancialExportsInfolist extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public Project $project;

    public array $informations = [];

    public function mount()
    {
        $this->informations = $this->project->globalCalculus();
    }

    protected function getYearlyInformations(int $year)
    {
        return $this->informations[$year] ?? [];
    }

    public function displayInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->project)
            ->columns(1)
            ->schema([
                $this->getGlobalInformations(),

                Tabs::make('Détails par année')
                    ->tabs(function () {
                        $schema = [];

                        foreach ($this->project->getPeriodOfAnalysis() as $year) {

                            $schema[] = Tabs\Tab::make($year)
                                ->schema(function () use ($year) {

                                    $yearlySchema = [];

                                    $yearlySchema[] = $this->getGlobalInformationsYearly(year: $year);

                                    $yearlySchema[] = $this->getRiskFieldset(year: $year);

                                    $yearlySchema[] = $this->getSponsorFieldset(year: $year);

                                    $yearlySchema[] = $this->getPartnersFieldset(year: $year);

                                    return $yearlySchema;
                                });
                        }

                        return $schema;
                    }),

                $this->getTechnicalFieldset(),

            ]);
    }

    public function getGlobalInformations(): Fieldset
    {
        return Fieldset::make('Informations globales')
            ->columns(3)
            ->schema([

                TextEntry::make('id')
                    ->formatStateUsing(fn () => $this->project->getPlannedAuditYear())
                    ->label("Année prévisionnelle d'audit"),

                TextEntry::make('id')
                    ->formatStateUsing(fn () => format($this->project->getDonations(mode: 'HT')).' € HT')
                    ->label('Totales contributions fléchées'),

            ]);
    }

    public function getGlobalInformationsYearly(int $year)
    {
        $yearlyInformations = $this->getYearlyInformations(year: $year);

        return Fieldset::make('Informations annuelles globales')
            ->columns(4)
            ->schema([

                TextEntry::make('id')
                    ->formatStateUsing(fn () => format(Arr::get($yearlyInformations, 'donations')).' € HT')
                    ->label('Totales contributions fléchées'),

                TextEntry::make('id')
                    ->formatStateUsing(fn () => format(Arr::get($yearlyInformations, 'tenant.real_amount')).' € HT')
                    ->label('Commission antenne'),

                TextEntry::make('tenant_commission_type')
                    ->formatStateUsing(fn () => $this->project->tenant_commission_type?->displayName())
                    ->placeholder('Non définie')
                    ->label('Type de commission'),

                TextEntry::make('id')
                    ->formatStateUsing(fn () => match ($this->project->tenant_commission_type) {
                        CommissionTypeEnum::Percentage => $this->project->tenant_commission_percentage.' %',
                        CommissionTypeEnum::Numerical => format($this->project->tenant_commission_numerical).' €',
                        default => 'Non définie'
                    })
                    ->placeholder('Non définie')
                    ->label('Référence calcul commission'),
            ]);
    }

    public function getRiskFieldset(int $year): Fieldset
    {
        $yearlyInformations = $this->getYearlyInformations(year: $year);

        return Fieldset::make('Risque')
            ->columns(4)
            ->schema([

                TextEntry::make('id')
                    ->formatStateUsing(fn () => format(Arr::get($yearlyInformations, 'ca')).' € HT')
                    ->placeholder('0 € HT')
                    ->label("Chiffre d'affaire antenne"),

                TextEntry::make('id')
                    ->formatStateUsing(fn () => format(Arr::get($yearlyInformations, 'risk.value')).' € HT')
                    ->hintAction(
                        Action::make('help')
                            ->icon('heroicon-o-question-mark-circle')
                            ->color('gray')
                            ->label('')
                            ->tooltip('Risques : '.Arr::join(Arr::get($yearlyInformations, 'risk.type'), ', ', ', '))
                    )
                    ->placeholder('0 € HT')
                    ->label('Risque annuel'),

                TextEntry::make('contracts_with_obligation_to_achieve_results')
                    ->formatStateUsing(fn () => format($this->project->getContractsWithObligationToAchieveResults(year: $year) * 100).' %')
                    ->placeholder('0 % (non renseigné)')
                    ->label('% de contrats avec obl...'),

                TextEntry::make('credit_characteristics')
                    ->formatStateUsing(fn () => $this->project->credit_characteristics?->displayName())
                    ->placeholder('Aucune donnée')
                    ->label('Type de crédit'),

            ]);
    }

    public function getSponsorFieldset(int $year): Fieldset
    {
        $yearlyInformations = $this->getYearlyInformations(year: $year);

        return Fieldset::make('Porteur du projet')
            ->columns(4)
            ->schema([
                TextEntry::make('sponsor.name')
                    ->label('Porteur du projet'),

                TextEntry::make('subject_to_vat')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return 'Oui';
                        }

                        return 'Non';
                    })
                    ->label('Assujetti à la TVA'),

                TextEntry::make('id')
                    ->formatStateUsing(fn () => format(Arr::get($yearlyInformations, 'project_holder.real_amount')).' € HT')
                    ->placeholder(0 .' €')
                    ->copyable()
                    ->label('Total dû au porteur'),

                TextEntry::make('id')
                    ->formatStateUsing(fn () => format($this->project->getProjectHolderRealAmount(year: $year)).' € HT')
                    ->placeholder(0 .' €')
                    ->copyable()
                    ->label('Total versé au porteur'),
            ]);
    }

    public function getPartnersFieldset(int $year): Fieldset
    {
        $schema = [];

        $partnersProject = $this->project->projectPartners()->get();

        $yearlyInformations = $this->getYearlyInformations(year: $year);

        foreach ($partnersProject as $partnerProject) {
            $schema[] = Section::make($partnerProject->partner->name)
                ->collapsible()
                ->collapsed(false)
                ->compact()
                ->columns(2)
                ->schema([

                    TextEntry::make('id')
                        ->formatStateUsing(fn () => format(Arr::get($yearlyInformations, 'partners.'.$partnerProject->partner_id.'.real_amount')).' € HT')
                        ->placeholder(0 .' €')
                        ->copyable()
                        ->label('Total dû au partenaire'),

                    TextEntry::make('id')
                        ->formatStateUsing(fn () => format($this->project->getPartnerRealAmount(partnerProject: $partnerProject, year: $year)).' € HT')
                        ->placeholder(0 .' €')
                        ->copyable()
                        ->label('Total versé au partenaire'),

                ]);
        }

        return Fieldset::make('Partenaires')
            ->schema($schema);
    }

    public function getTechnicalFieldset(): Section
    {
        return Section::make('Informations techniques Eliott')
            ->collapsed()
            ->collapsible()
            ->compact()
            ->schema([
                TextEntry::make('id')
                    ->formatStateUsing(fn () => json_encode($this->informations))
                    ->copyable()
                    ->columnSpanFull()
                    ->label('Details'),
            ]);
    }

    public function render()
    {
        return view('livewire.infolists.projects.financial-exports-infolist');
    }
}
