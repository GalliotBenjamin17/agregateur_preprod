<?php

namespace App\Exports;

use App\Models\DonationSplit;
use App\Models\Organization;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DonationSplitsExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //return DonationSplit::onlyParents() // Charger uniquement les parent
        return DonationSplit::query()
            ->with([
                'donation.related',
                'donation.tenant',
                'donation.transaction',
                'project.parentProject', // Charger le projet et son parent
                'projectCarbonPrice',

            ])
            ->get();
    }

    /**
     * @param  DonationSplit  $row
     */
    public function map($row): array
    {
        return [
            match ($row->donation->related ? get_class($row->donation->related) : null) {
                Organization::class => 'Entité',
                User::class => 'Particulier',
                default => 'Autre (borne, import, etc)'
            },
            $row->donation->related?->name ?? '-',
            $row->donation->tenant?->name,
            $row->donation->amount,
            $row->amount,
            ($row->project?->parentProject ? $row->project->parentProject->name.' - '.$row->project->name : $row->project?->name) ?? '-',
            $row->donation->created_at,
            $row->created_at,
            $row->projectCarbonPrice?->price,
            $row->tonne_co2,
            match ($row->donation->related ? get_class($row->donation->related) : null) {
                Organization::class => $row->donation->related->billing_email,
                User::class => $row->donation->related->email,
                default => ''
            },
            $row->donation->external_id,
            $row->donation->transaction?->payment_order_id ?? '',
            $row->donation->transaction?->order_id ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'Type de contributeur',
            'Relié à',
            'Instance locale',
            'Montant contribution parente (TTC)',
            'Montant fléchage (TTC)',
            'Projet fléché',
            'Date de contribution',
            'Date de fléchage',
            'Prix HT / tCo2 projet au moment du fléchage',
            'tCo2',
            'Email contributeur',
            '[PAYZEN] - External ID',
            '[PAYZEN] - Payment Order ID',
            '[PAYZEN] - Order ID',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_CURRENCY_EUR,
            'E' => NumberFormat::FORMAT_CURRENCY_EUR,
            'G' => NumberFormat::FORMAT_DATE_DATETIME,
            'H' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
