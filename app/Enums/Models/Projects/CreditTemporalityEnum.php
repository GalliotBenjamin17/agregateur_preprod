<?php

namespace App\Enums\Models\Projects;

enum CreditTemporalityEnum: string
{
    case ExAnte = 'ex_ante';
    case ExPost = 'ex_post';

    public function databaseKey(): string
    {
        return match ($this) {
            self::ExAnte => self::ExAnte->value,
            self::ExPost => self::ExPost->value,
        };
    }

    public function displayName(): string
    {
        return match ($this) {
            self::ExAnte => 'Ex Ante',
            self::ExPost => 'Ex Post',
        };
    }

    public function humanName(): string
    {
        return $this->displayName();
    }

    public static function toArray(): array
    {
        return [
            self::ExAnte->databaseKey() => CreditTemporalityEnum::ExAnte->displayName(),
            self::ExPost->databaseKey() => CreditTemporalityEnum::ExPost->displayName(),
        ];
    }

    public function description(): string
    {
        return match ($this) {
            self::ExAnte => 'Évaluation du crédit avant le financement',
            self::ExPost => 'Évaluation du crédit après le financement.',
        };
    }
}
