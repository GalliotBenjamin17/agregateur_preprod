<?php

namespace App\States;

use App\States\Certification\Approved;
use App\States\Certification\Certified;
use App\States\Certification\Evaluated;
use App\States\Certification\Notified;
use App\States\Certification\PendingEvaluation;
use App\States\Certification\Verified;
use App\States\Project\Submitted;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class CertificationState extends State
{
    abstract public function humanName(): string;

    abstract public function description(): string;

    abstract public function rank(): int;

    abstract public function icon(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Submitted::class)
            ->registerState([
                Notified::class,
                PendingEvaluation::class,
                Evaluated::class,
                Certified::class,
                Verified::class,
                Approved::class,
            ]);
    }
}
