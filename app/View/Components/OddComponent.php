<?php

namespace App\View\Components;

use App\Models\Project;
use App\Models\SustainableDevelopmentGoals;
use Illuminate\View\Component;

class OddComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $sustainableDevelopmentGoals;

    public function __construct(
        public Project $project
    ) {
        $this->sustainableDevelopmentGoals = SustainableDevelopmentGoals::all();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.odd-component');
    }
}
