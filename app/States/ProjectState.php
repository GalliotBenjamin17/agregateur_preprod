<?php

namespace App\States;

use App\States\Project\Abandoned;
use App\States\Project\Approved;
use App\States\Project\Archived;
use App\States\Project\Done;
use App\States\Project\Pending;
use App\States\Project\Submitted;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ProjectState extends State
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
                Submitted::class,
                Approved::class,
                Pending::class,
                Done::class,
                Archived::class,
                Abandoned::class,
            ]);
    }
}
