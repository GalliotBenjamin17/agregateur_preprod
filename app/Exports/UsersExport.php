<?php

namespace App\Exports;

use App\Enums\Roles;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::with([
            'tenant',
            'donations',
            'organizations',
        ])->get();
    }

    public function map($row): array
    {
        return [
            ['m' => 'M.', 'f' => 'Mme', '' => '-'][$row->gender] ?? '',
            $row->first_name,
            $row->last_name,
            $row->email,
            $row->phone,
            $row->tenant?->name,
            $row->created_at,
            $row->roles->pluck('name')->map(fn ($value) => \Arr::get(Roles::toDisplay(), $value))->join(', ', ' et '),
            $row->organizations->pluck('name')->join(', '),
            $row->donations->sum('amount'),
        ];
    }

    public function headings(): array
    {
        return [
            'Civilité',
            'Prénom',
            'Nom',
            'Email',
            'Téléphone',
            'Instance locale',
            'Création',
            'Roles',
            'Organisations',
            'Contributions',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DMYSLASH,
            'J' => NumberFormat::FORMAT_CURRENCY_EUR,
        ];
    }
}
