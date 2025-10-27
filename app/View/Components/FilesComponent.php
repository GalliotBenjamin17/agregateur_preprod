<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class FilesComponent extends Component
{
    public function __construct(
        public Model $model,
        public bool $inColumn = false
    ) {
        $this->model->load([
            'files',
        ])->loadCount([
            'files',
        ]);
    }

    public function render(): View
    {
        return view('components.files-component');
    }
}
