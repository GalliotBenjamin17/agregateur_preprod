<?php

namespace App\View\Components\Emails;

use Illuminate\View\Component;

class Base extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.emails.base')->with([
            'logo' => setting('logo', 'img/logos/cooperative-carbone/logo_png.png'),
            'name' => setting('name', 'Coopérative Carbone'),
            'accentColor' => setting('brand_color', '#244999'),
        ]);
    }
}
