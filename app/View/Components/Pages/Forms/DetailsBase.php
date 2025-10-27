<?php

namespace App\View\Components\Pages\Forms;

use App\Models\Form;
use Illuminate\View\Component;
use Illuminate\View\View;

class DetailsBase extends Component
{
    public function __construct(
        public Form $form
    ) {
        $form->loadCount([
            'formFields',
        ]);
    }

    public function render(): View
    {
        return view('app.forms.details.layouts.base');
    }
}
