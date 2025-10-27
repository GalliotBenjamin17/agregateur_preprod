<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Spatie\Activitylog\Models\Activity;

class ActivitiesModel extends Component
{
    public Collection|array $activities = [];

    public function __construct(
        public Model $model,
        public int $limit = 100
    ) {
        $this->activities = Activity::where('subject_type', get_class($this->model))->where('subject_id', $this->model->id)
            ->orderBy('created_at', 'desc')
            ->take($this->limit)
            ->get();
    }

    public function render(): View
    {
        return view('components.activities-model');
    }
}
