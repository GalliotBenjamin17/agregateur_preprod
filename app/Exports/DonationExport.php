<?php

namespace App\Exports;

use App\Helpers\TVAHelper;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DonationExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Donation::with([
            'tenant',
            'related',
            'transaction',
            'donationSplits.project',
        ])->get();
    }

    /**
     * @param  Donation  $row
     */
    public function map($row): array
    {
        return [
            match ($row->related ? get_class($row->related) : null) {
                Organization::class => 'Entité',
                User::class => 'Particulier',
                default => 'Autre (borne, import, etc)'
            },
            $row->related?->name ?? '-',
            $row->tenant?->name,
            TVAHelper::getHT($row->amount),
            $row->amount,
            $row->donationSplits()->onlyParents()->sum('amount'),
            $row->donationSplits()->onlyParents()->with('project')->get()->pluck('project')->unique('id')->pluck('name')->join(',', ','),
            $row->created_at,
            $row->donationSplits()->onlyParents()->sum('tonne_co2'),
            match ($row->related ? get_class($row->related) : null) {
                Organization::class => $row->related->billing_email,
                User::class => $row->related->email,
                default => ''
            },
            $row->external_id,
            $row->transaction?->payment_order_id ?? '',
            $row->transaction?->order_id ?? '',
            $row->source,
        ];
    }

    public function headings(): array
    {
        return [
            'Type de contributeur',
            'Relié à',
            'Instance locale',
            'Montant HT',
            'Montant TTC',
            'Montant fléché',
            'Projets fléchés',
            'Date de contribution',
            'Tonnage (tCo2)',
            'Email contributeur',
            '[PAYZEN] - External ID',
            '[PAYZEN] - Payment Order ID',
            '[PAYZEN] - Order ID',
            'Mode de paiement',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_CURRENCY_EUR,
            'E' => NumberFormat::FORMAT_CURRENCY_EUR,
            'F' => NumberFormat::FORMAT_CURRENCY_EUR,
            'G' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
