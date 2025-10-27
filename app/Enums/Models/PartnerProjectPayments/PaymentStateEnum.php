<?php

namespace App\Enums\Models\PartnerProjectPayments;

enum PaymentStateEnum: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sent = 'sent';

    public function databaseKey(): string
    {
        return match ($this) {
            self::Draft => self::Draft->value,
            self::Scheduled => self::Scheduled->value,
            self::Sent => self::Sent->value,
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Scheduled => 'Planifié',
            self::Sent => 'Effectué',
        };
    }

    public static function toArray(): array
    {
        return [
            self::Draft->databaseKey() => PaymentStateEnum::Draft->displayName(),
            self::Scheduled->databaseKey() => PaymentStateEnum::Scheduled->displayName(),
            self::Sent->databaseKey() => PaymentStateEnum::Sent->displayName(),
        ];
    }
}
