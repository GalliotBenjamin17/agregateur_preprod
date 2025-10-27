<?php

namespace App\Enums\Models\Projects;

enum CarbonCreditCharacteristicsEnum: string
{
    case Avoidance = 'avoidance';
    case Sequestration = 'sequestration';
    case TotalGains = 'total_gains';

    public function databaseKey(): string
    {
        return $this->value;
    }

    public function displayName(): string
    {
        return match ($this) {
            self::Avoidance => 'Évitement',
            self::Sequestration => 'Séquestration',
            self::TotalGains => 'Réduction',
        };
    }

    public function humanName(): string
    {
        return $this->displayName();
    }

    public static function toArray(): array
    {
        return [
            self::Avoidance->databaseKey() => CarbonCreditCharacteristicsEnum::Avoidance->displayName(),
            self::Sequestration->databaseKey() => CarbonCreditCharacteristicsEnum::Sequestration->displayName(),
            self::TotalGains->databaseKey() => CarbonCreditCharacteristicsEnum::TotalGains->displayName(),
        ];
    }
}
