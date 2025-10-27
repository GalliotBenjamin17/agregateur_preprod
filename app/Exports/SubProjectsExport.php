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

class SubProjectsExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
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
            'parentProject.certification',
            'parentProject.segmentation',
            'parentProject.methodForm',
            'methodForm',
            'activeCarbonPrice',
        ])->withCount([
            'childrenProjects',
        ])->whereNotNull('parent_project_id')
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
            $row->parentProject->name,
            $row->name,
            $row->tenant?->name,
            $row->donationSplits->sum('amount'),
            $row->donationSplits->sum('tonne_co2'),
            $row->cost_duration_years,
            $projectFullAddress,
            $row->activeCarbonPrice?->price,
            $row->parentProject?->segmentation?->name ?? '',
            $row->state->humanName(),
            $row->parentProject?->methodForm?->name ?? '',
            $row->certification_state->humanName(),
            $row->parentProject?->certification?->name ?? '',
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
            'Projet parent',
            'Nom',
            'Instance locale',
            'Contributions € TTC',
            'Contributions tCo2',
            'Durée du projet (années)',
            'Localisation',
            'Prix crédit carbone HT plateforme (actuel)',
            'Segmentation (parent',
            'Statut principal',
            'Méthode (parent)',
            'Statut méthode',
            'Label (parent)',
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
            'D' => NumberFormat::FORMAT_CURRENCY_EUR, // Contributions €
            'H' => NumberFormat::FORMAT_CURRENCY_EUR, // Prix crédit carbone HT plateforme (actuel)
        ];
    }
}
