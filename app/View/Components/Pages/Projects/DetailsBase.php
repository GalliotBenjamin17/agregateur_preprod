<?php

namespace App\View\Components\Pages\Projects;

use App\Models\Project;
use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public Project $project
    ) {
        $this->project->loadMissing([
            'createdBy',
            'tenant',
            'sponsor',
            'certification',
        ]);
    }

    public function render(): View
    {
        return view('app.projects.details.layouts.base', [
            'donationSplitsCount' => $this->project->donationSplits()->count()
        ]);
    }
}
