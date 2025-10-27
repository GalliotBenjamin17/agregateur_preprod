<?php

namespace App\Enums\Models\Partners;

enum CommissionTypeEnum: string
{
    case Percentage = 'percentage';
    case Numerical = 'numerical';

    public function databaseKey(): string
    {
        return match ($this) {
            self::Percentage => self::Percentage->value,
            self::Numerical => self::Numerical->value,
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::Percentage => 'Pourcentage',
            self::Numerical => 'Numéraire',
        };
    }

    public static function toArray(): array
    {
        return [
            self::Percentage->databaseKey() => CommissionTypeEnum::Percentage->displayName(),
            self::Numerical->databaseKey() => CommissionTypeEnum::Numerical->displayName(),
        ];
    }

    public function description(): string
    {
        return match ($this) {
            self::Percentage => 'Commission calculée sur le montant reversé au porteur de projet',
            self::Numerical => 'Commission fixe définie en amont.',
        };
    }
}
