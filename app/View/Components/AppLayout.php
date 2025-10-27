<?php

namespace App\View\Components;

use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public Collection $reminders;

    public function __construct()
    {
        $this->reminders = Reminder::with('createdBy')
            ->whereDate('reminder_at', '>=', Carbon::today()->startOfDay())
            ->orderBy('reminder_at')
            ->get();
    }

    public function render()
    {
        return view('layouts.app');
    }
}
