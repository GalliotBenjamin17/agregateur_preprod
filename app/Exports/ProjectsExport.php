<?php

namespace App\Exports;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProjectsExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Project::with([
            'tenant',
            'donationSplits',
            'certification',
            'sponsor',
            'segmentation',
            'referent',
            'auditor',
            'parentProject',
            'methodForm',
            'activeCarbonPrice',
        ])->withCount([
            'childrenProjects',
        ])->whereNull('parent_project_id')
            ->get();
    }

    public function map($row): array
    {
        $projectAddressParts = [
            $row->address_1,
            $row->address_2,
            $row->address_postal_code,
            $row->address_city,
        ];
        $projectFullAddress = implode(' ', array_filter($projectAddressParts));

        return [
            $row->name,
            $row->tenant?->name,
            $row->donationSplits->sum('amount'),
            ((float) $row->cost_global_ttc > 0) ? format(($row->donationSplits->sum('amount') / $row->cost_global_ttc * 100), 2) : 0,
            $row->cost_global_ttc,
            $row->donationSplits->sum('tonne_co2'),
            match ($row->hasChildrenProjects()) {
                true => match ((bool) $row->is_goal_tco2_edited_manually) {
                    true => $row->tco2,
                    false => $row->childrenProjects()->sum('tco2')
                },
                false => $row->tco2
            },
            $row->cost_duration_years,
            $projectFullAddress,
            $row->activeCarbonPrice?->price,
            $row->segmentation?->name ?? '',
            $row->state->humanName(),
            $row->methodForm?->name ?? '',
            $row->certification_state->humanName(),
            $row->certification?->name ?? '',
            match ($row->sponsor ? get_class($row->sponsor) : null) {
                Organization::class => 'Organisation',
                User::class => 'Particulier',
                null => 'Inconnu',
            },
            match ($row->sponsor ? get_class($row->sponsor) : null) {
                Organization::class => $row->sponsor?->name ?? '',
                User::class => $row->sponsor?->name ?? '',
                null => 'Inconnu',
            },
            ($row->sponsor instanceof Organization) ? $row->sponsor?->siret : '',
            match ($row->sponsor ? get_class($row->sponsor) : null) {
                Organization::class => $row->sponsor?->billing_email ?? '',
                User::class => $row->sponsor?->email ?? '',
                null => 'Inconnu',
            },
            $row->referent?->name ?? '',
            $row->referent?->email ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Instance locale',
            'Contributions €',
            'Pourcentage de financement',
            'Coût global TTC',
            'Contributions tCo2',
            'Objectif tCo2',
            'Durée du projet (années)',
            'Localisation',
            'Prix crédit carbone HT plateforme (actuel)',
            'Segmentation',
            'Statut principal',
            'Methode',
            'Statut méthode',
            'Label',
            'Type de porteur',
            'Porteur',
            'SIRET porteur',
            'Mail du porteur',
            'Référent',
            'Mail du référent',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_CURRENCY_EUR, // Contributions €
            'E' => NumberFormat::FORMAT_CURRENCY_EUR, // Coût global TTC
            'K' => NumberFormat::FORMAT_CURRENCY_EUR, // Prix crédit carbone HT plateforme (actuel)

        ];
    }
}
